<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Employee;
use common\models\Lead;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use common\models\UserParams;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Da\QrCode\QrCode;
use Da\TwoFA\Manager;

use Da\TwoFA\Service\TOTPSecretKeyUriGeneratorService;
use Da\TwoFA\Service\QrCodeDataUriGeneratorService;

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
                        'actions' => ['login', 'error', 'mfa'],
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

    public function actionMfa()
    {
        $secret = (new Manager())->generateSecretKey();

        $totpUri = (new TOTPSecretKeyUriGeneratorService('your-company', 'andrew.snake@techork.com', $secret))->run();
        $uri = (new QrCodeDataUriGeneratorService($totpUri))->run();

        return $this->render('mfa', [
            'uri' => $uri,
        ]);
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
     */
    public function actionLogin()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();



        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login.php', [
            'model' => $model,
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

        /** @var Employee $model */
        $model = Yii::$app->user->identity;

        if (!$model) {
            throw new NotFoundHttpException('The requested User does not exist.');
        }

        $modelUserParams = $model->userParams;
        if (!$modelUserParams) {
            $modelUserParams = new UserParams();
        }


        //Yii::$app->request->isPost
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //$attr = Yii::$app->request->post($model->formName());

            if (!empty($this->password)) {
                $this->setPassword($this->password);
            }


            if ($modelUserParams->load(Yii::$app->request->post()) && $modelUserParams->validate()) {
                $modelUserParams->save();
            }
            //$attr = Yii::$app->request->post($model->formName());

            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }
            //$model->prepareSave($attr);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Profile successful updated!');
                $model->refresh();
            }

        }

        //new UserParams();


        $secureCode = md5(Yii::$app->user->id . '|' . Yii::$app->user->identity->username . '|' . date('Y-m-d'));


        //$host = (Yii::$app->request->isSecureConnection ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        //$url = \yii\helpers\Url::to(['site/telegram-activate', 'id' => Yii::$app->user->id, 'code' => $secureCode]);

        //echo $host.$url; exit;
        $code = base64_encode(Yii::$app->user->id . '|' . $secureCode);

        $url = 'https://telegram.me/CrmKivorkBot?start='.$code;

        $qrCode = (new QrCode($url))
            ->setSize(160)
            ->setMargin(5)
            ->useForegroundColor(0, 0, 0);



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
            'qrcodeData' => $qrCode->writeDataUri()
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
