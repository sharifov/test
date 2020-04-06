<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Employee;
use common\models\Lead;
use common\models\LoginStepTwoForm;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use common\models\UserBonusRules;
use common\models\UserCommissionRules;
use common\models\UserParams;
use frontend\models\form\UserProfileForm;
use sales\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Da\QrCode\QrCode;
use Da\TwoFA\Manager;

use Da\TwoFA\Service\TOTPSecretKeyUriGeneratorService;
use Da\TwoFA\Service\QrCodeDataUriGeneratorService;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'step-two'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'profile', 'get-airport', 'blank'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['POST', 'GET'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '@frontend/themes/gentelella_v2/views/site/error',
                'layout' => '@frontend/themes/gentelella_v2/views/layouts/error'
            ],
        ];
    }

    /**
     *
     */
    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        return $this->render('index', ['user' => $user]);
    }


    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Login action.
     *
     * @return string
     * @throws \Da\TwoFA\Exception\InvalidSecretKeyException
     */
    public function actionLogin()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/login';
        $twoFactorAuthEnable = Yii::$app->params['settings']['two_factor_authentication_enable'];
        $twoFactorAuthCounter = Yii::$app->params['settings']['two_factor_counter'] ?? 60;

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $user = $model->checkedUser()) {
            if ($twoFactorAuthEnable && $user->userProfile->is2faEnable()) {
                $twoFaManager = new Manager();
                $twoFaManager->setCounter($twoFactorAuthCounter);
                $twoFactorAuthSecretKey = empty($user->userProfile->up_2fa_secret) ?
                    $twoFaManager->generateSecretKey() : $user->userProfile->up_2fa_secret;

                $session = Yii::$app->session;
                $session->set('two_factor_email', $user->email);
                $session->set('two_factor_key_exist', !empty($user->userProfile->up_2fa_secret));
                $session->set('two_factor_key', $twoFactorAuthSecretKey);
                $session->set('two_factor_remember_me', $model->rememberMe);
                return $this->redirect(['site/step-two']);
            }

            if ($model->login()) {
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login.php', [
            'model' => $model,
        ]);
    }


    /**
     * @return string|Response
     */
    public function actionStepTwo()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/login';
        $session = Yii::$app->session;

        if (!$session->has('two_factor_email') || !$session->has('two_factor_key')) {
            return $this->redirect(['site/login']);
        }

        $userEmail = $session->get('two_factor_email');
        $twoFactorAuthKey = $session->get('two_factor_key');

        $model = (new LoginStepTwoForm())
            ->setUserEmail($userEmail)
            ->setTwoFactorAuthKey($twoFactorAuthKey)
            ->setRememberMe($session->get('two_factor_remember_me'));

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $totpUri = (new TOTPSecretKeyUriGeneratorService('travelinsides.com', $userEmail, $twoFactorAuthKey))->run();
        $qrcodeSrc = (new QrCodeDataUriGeneratorService($totpUri))->run();

        return $this->render('step-two', [
            'qrcodeSrc' => $qrcodeSrc,
            'model' => $model,
            'twoFactorKeyExist' => $session->get('two_factor_key_exist'),
        ]);
    }


    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionProfile(): string
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $model = Employee::findOne(Yii::$app->user->id);
        if (!$model) {
            throw new NotFoundHttpException('The requested User does not exist.');
        }

        $userProfileForm = new UserProfileForm();
        $userProfileForm->email = $model->email;
        $userProfileForm->full_name = $model->full_name;
        $userProfileForm->password = '';

        $modelUserParams = $model->userParams;
        if (!$modelUserParams) {
            $modelUserParams = new UserParams();
        }

        if (Yii::$app->request->isPost) {
            $updated = 0;
            if ($userProfileForm->load(Yii::$app->request->post()) && $userProfileForm->validate()) {
                if (!empty($userProfileForm->password)) {
                    $model->setPassword($userProfileForm->password);
                }
                if ($model->email !== $userProfileForm->email) {
                    $model->email = $userProfileForm->email;
                }
                $model->full_name = $userProfileForm->full_name;

                try {
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Profile successful updated');
                        $model->refresh();
                        $updated++;
                    }
                } catch (\Throwable $throwable) {
                    Yii::error(AppHelper::throwableFormatter($throwable), 'SiteController:actionProfile:updProfile' );
                }
            }

            if ($modelUserParams->load(Yii::$app->request->post()) && $modelUserParams->validate()) {
                if ($modelUserParams->save()) {
                    Yii::$app->session->setFlash('success', 'Profile updated');
                    $updated++;
                }
            }
            if ($updated) {
                $this->redirect('/site/profile');
            }
        }

        $secureCode = md5(Yii::$app->user->id . '|' . Yii::$app->user->identity->username . '|' . date('Y-m-d'));


        //$host = (Yii::$app->request->isSecureConnection ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        //$url = \yii\helpers\Url::to(['site/telegram-activate', 'id' => Yii::$app->user->id, 'code' => $secureCode]);

        //echo $host.$url; exit;
        $code = base64_encode(Yii::$app->user->id . '|' . $secureCode);

        $url = 'https://telegram.me/CrmKivorkBot?start='.$code;

        $qrCode = (new QrCode($url))
            ->setSize(160)
            ->setMargin(5);

		$expMonth = $modelUserParams->upUser->userProfile->getExperienceMonth();
		$userCommissionRulesValue = UserCommissionRules::find()->getCommissionValueByExpMonth($expMonth);
		$userBonusRulesValue = UserBonusRules::find()->getBonusValueByExpMonth($expMonth);


        /*$qrCode = (new QrCode('https://2amigos.us'))
            ->useLogo(__DIR__ . '/data/logo.png')
            ->useForegroundColor(51, 153, 255)
            ->useBackgroundColor(200, 220, 210)
            ->useEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
            ->setLogoWidth(60)
            ->setSize(300)
            ->setMargin(5)
            ->setLabel($label);*/

        //$qrCode->writeFile(__DIR__ . '/codes/my-code.png');

        // now we can display the qrcode in many ways
        // saving the result to a file:

        //$qrCode->writeFile(__DIR__ . '/code.png'); // writer defaults to PNG when none is specified

        // display directly to the browser
        //header('Content-Type: '.$qrCode->getContentType());
        //echo $qrCode->writeString();
        //echo $qrCode->writeDataUri();
        //exit;


        return $this->render('/employee/update_profile', [
            'model' => $model,
            'modelUserParams' => $modelUserParams,
            'qrcodeData' => $qrCode->writeDataUri(),
            'userProfileForm' => $userProfileForm,
			'userCommissionRuleValue' => $userCommissionRulesValue,
			'userBonusRuleValue' => $userBonusRulesValue,
        ]);
    }

    public function actionGetAirport($term)
    {
        $response = file_get_contents(sprintf('%s?term=%s', Yii::$app->params['getAirportUrl'], $term));
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            if (isset($response['data'])) {
                foreach ($response['data'] as $key => $item) {
                    $response['data'][$key]['value'] = sprintf('%s (%s)', $item['city'], $item['iata']);
                }
                return json_encode($response['data']);
            }
        }

        return json_encode([]);
    }

}
