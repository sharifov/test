<?php

namespace frontend\controllers;

use common\components\TwoFactorService;
use common\models\ApiLog;
use common\models\Employee;
use common\models\Lead;
use common\models\LoginStepTwoForm;
use common\models\query\EmployeeQuery;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use common\models\UserBonusRules;
use common\models\UserCommissionRules;
use common\models\UserConnection;
use common\models\UserParams;
use Endroid\QrCode\Builder\Builder;
use frontend\models\form\UserProfileForm;
use frontend\themes\gentelella_v2\widgets\SideBarMenu;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\helpers\twoFactorAuth\TwoFactorAuthHelper;
use src\model\userAuthClient\entity\UserAuthClientQuery;
use src\model\userAuthClient\entity\UserAuthClientSources;
use src\model\user\entity\monitor\UserMonitor;
use src\useCase\login\twoFactorAuth\abac\TwoFactorAuthAbacObject;
use src\useCase\login\twoFactorAuth\TwoFactorAuthFactory;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\captcha\CaptchaAction;

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
                        'actions' => ['login', 'error', 'step-two', 'captcha', 'auth', 'auth-step-two'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'profile', 'get-airport', 'blank', 'side-bar-menu', 'auth-assign'],
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
            'captcha' => [
                'class' => CaptchaAction::class,
                'maxLength' => 6,
                'minLength' => 5,
                'transparent' => true,
                'offset' => 3,
                'foreColor' => hexdec('596b7d'),
            ],
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
            'auth-assign' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'onAuthAssignSuccess']
            ]
        ];
    }

    /**
     *
     */
    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        $sourcesDataProvider = new ActiveDataProvider();
        if (SettingHelper::isEnabledAuthClients()) {
            $authClients = UserAuthClientQuery::findAllByUserId(Auth::id());
            $sourcesDataProvider->setModels($authClients);
            $sourcesDataProvider->pagination = false;
        }
        return $this->render('index', ['user' => $user, 'sourcesDataProvider' => $sourcesDataProvider]);
    }


    public function actionLogout()
    {
        $userId = Yii::$app->user->id;
        if (Yii::$app->user->logout()) {
            LoginForm::removeWsIdentityCookie();

            if (Yii::$app->request->get('type') === 'autologout') {
                Yii::$app->session->setFlash('warning', 'The system has automatically logged out of your account!');
            }

            if (UserConnection::isIdleMonitorEnabled()) {
                UserMonitor::addEvent($userId, UserMonitor::TYPE_LOGOUT);
            }
        }



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

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $user = $model->checkedUser()) {
            if (SettingHelper::isTwoFactorAuthEnabled() && $this->get2FAAbacAccess($user) && $user->userProfile /*&& $user->userProfile->is2faEnable()*/) {
                return $this->redirectToTwoFactorAuth($user, $model);
            }

            if ($model->login()) {
                if (UserConnection::isIdleMonitorEnabled()) {
                    UserMonitor::addEvent(Yii::$app->user->id, UserMonitor::TYPE_LOGIN);
                }

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

        if (!$session->has('two_factor_email')) {
            return $this->redirect(['site/login']);
        }
        $userEmail = $session->get('two_factor_email');

        $user = EmployeeQuery::findByEmail($userEmail);
        if (!$user && !$user->userProfile) {
            return $this->redirect(['site/login']);
        }

        $model = (new LoginStepTwoForm())
            ->setUser($user)
            ->setRememberMe($session->get('two_factor_remember_me'));

        $attemptsRemain = TwoFactorAuthHelper::getAuthAttempts();
        if ($model->load(Yii::$app->request->post()) && !Yii::$app->request->isPjax && $model->validate()) {
            $twoFactorAuthForm = (TwoFactorAuthFactory::getForm($model->twoFactorMethod))->setUser($user);
            $twoFactorAuthForm->load(Yii::$app->request->post());
            if (($twoFactorAuthForm->validate() && $twoFactorAuthForm->login((bool)$model->rememberMe))) {
                TwoFactorAuthHelper::removeAuthAttempts();
                $session->remove('auth_client_source');
                $session->remove('auth_client_source_id');
                return $this->goHome();
            } else {
                if (--$attemptsRemain > 0) {
                    Yii::warning(
                        'Wrong step two code for user: ' . $user->id,
                        'SiteController:actionStepTwo',
                    );
                    TwoFactorAuthHelper::setAuthAttempts($attemptsRemain);
                } else {
                    $user->setBlocked();
                    if (!$user->save(false)) {
                        \Yii::error(
                            VarDumper::dumpAsString($user->getErrors(), 10),
                            'SiteController:actionStepTwo'
                        );
                    } else {
                        Yii::warning(
                            'Step two attempts exceeded for user: ' . $user->id,
                            'SiteController:actionStepTwo',
                        );
                    }
                    TwoFactorAuthHelper::removeAuthAttempts();
                    return $this->redirect(['site/login']);
                }
            }
            $model->addError('general', $twoFactorAuthForm->getErrorSummary(true)[0]);
        }

        if (empty($model->twoFactorMethod)) {
            $model->twoFactorMethod = TwoFactorAuthFactory::getDefaultAuthMethod($user);
            if (empty($model->twoFactorMethod)) {
                Yii::$app->session->removeAll();
                Yii::$app->session->setFlash('error', 'You do not have access to any two-factor authentication method.');
                return $this->redirect(['/site/login']);
            }
        }
        $helper = TwoFactorAuthFactory::getViewHelper($model->twoFactorMethod);
        return $this->render('step-two', [
            'model' => $model,
            'viewHelper' => $helper,
            'user' => $user,
            'attemptsRemain' => [
                'show' => TwoFactorAuthHelper::showWarningAttemptsRemain(),
                'remain' => TwoFactorAuthHelper::getAuthAttempts(),
            ],
        ]);
    }


    /**
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $model = Employee::findOne(Yii::$app->user->id);
        if (!$model) {
            throw new NotFoundHttpException('The requested User does not exist.');
        }

        $userProfileForm = new UserProfileForm();
        $userProfileForm->username = $model->username;
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
                    $logData = AppHelper::throwableLog($throwable);
                    $logData['userId'] = (int) $model->id;
                    Yii::error($logData, 'SiteController:actionProfile:updProfile');
                }
            }

            if ($modelUserParams->load(Yii::$app->request->post()) && $modelUserParams->validate()) {
                if ($modelUserParams->save()) {
                    Yii::$app->session->setFlash('success', 'Profile updated');
                    $updated++;
                }
            }
            if ($updated) {
                return $this->redirect('/site/profile');
            }
        }

        $secureCode = md5(Yii::$app->user->id . '|' . Yii::$app->user->identity->username . '|' . date('Y-m-d'));
        $code = base64_encode(Yii::$app->user->id . '|' . $secureCode);


        if (Yii::$app->telegram && !empty(Yii::$app->telegram->botUsername)) {
            $url = 'https://telegram.me/' . trim(Yii::$app->telegram->botUsername) . '?start=' . $code;

            $result = Builder::create()
                ->data($url)
                ->size(170)
                ->margin(0)
                ->build();

            $qrCodeData = $result->getDataUri();
        } else {
            $qrCodeData = null;
        }

        $expMonth = $modelUserParams->upUser->userProfile->getExperienceMonth();
        $userCommissionRulesValue = UserCommissionRules::find()->getCommissionValueByExpMonth($expMonth);
        $userBonusRulesValue = UserBonusRules::find()->getBonusValueByExpMonth($expMonth);

        $sourcesDataProvider = new ActiveDataProvider();
        if (SettingHelper::isEnabledAuthClients()) {
            $sources = UserAuthClientQuery::findAllByUserId(Auth::id());
            $sourcesDataProvider->setModels($sources);
            $sourcesDataProvider->pagination = false;
        }

        return $this->render('/employee/update_profile', [
            'model' => $model,
            'modelUserParams' => $modelUserParams,
            'qrcodeData' => $qrCodeData,
            'userCommissionRuleValue' => $userCommissionRulesValue,
            'userBonusRuleValue' => $userBonusRulesValue,
            'userProfileForm' => $userProfileForm,
            'sourcesDataProvider' => $sourcesDataProvider
        ]);
    }

    public function actionGetAirport($term)
    {
        $response = file_get_contents(sprintf('%s?term=%s', Yii::$app->params['backOffice']['serverUrlV2'] . '/airport/search', $term));
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

    /**
     * @return string
     */
    public function actionSideBarMenu(): string
    {
        //$id = Yii::$app->request->get('id');
        // $status = Yii::$app->request->get('status');

        // $keyCache = 'cal-box-request-' . $id . '-' . $status;

        //Yii::$app->cache->delete($keyCache);

        //$result = Yii::$app->cache->get($keyCache);

        //if($result === false) {

        $box = SideBarMenu::getInstance();

        /*if($result) {
            Yii::$app->cache->set($keyCache, $result, 30);
        }*/
        //}

        //VarDumper::dump($data); exit;

        return $box->run();
    }

    public function actionAuthStepTwo()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/login';
        $session = Yii::$app->session;

        $source = (int)$session->get('auth_client_source');
        $sourceId = (string)$session->get('auth_client_source_id');

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!empty($source) && !empty($sourceId)) {
            $userId = Yii::$app->request->get('user-id');
            if ($userId && $authClient = UserAuthClientQuery::findByUserAndSource((int)$userId, $source, $sourceId)) {
                $form = new LoginForm();
                $form->setUser($authClient->user);
                $form->setUserChecked(true);

                if (SettingHelper::isTwoFactorAuthEnabled() && $authClient->user->userProfile && $this->get2FAAbacAccess($authClient->user)) {
                    return $this->redirectToTwoFactorAuth($authClient->user, $form);
                }

                if ($form->login()) {
                    if (UserConnection::isIdleMonitorEnabled()) {
                        UserMonitor::addEvent($userId, UserMonitor::TYPE_LOGIN);
                    }
                }
                $session->remove('auth_client_source');
                $session->remove('auth_client_source_id');
                return $this->goBack();
            } elseif (!empty($userId) && empty($authClient)) {
                throw new BadRequestHttpException('Bad Request');
            }


            if ($authClients = UserAuthClientQuery::findAllBySourceData($source, $sourceId)) {
                return $this->render('auth_step_two', [
                    'authClients' => $authClients
                ]);
            }
            Yii::$app->session->setFlash('error', 'Not found any user assigned with this auth client: ' . UserAuthClientSources::getName($source));
        }
        return $this->redirect('/site/login');
    }

    /**
     * @param ClientInterface $client
     * @return void
     */
    public function onAuthSuccess(ClientInterface $client)
    {
        $source = UserAuthClientSources::clientSourceFactory($client->getId());
        $source->handle($this->action, $client);
        return $this->action->redirect($source->getRedirectUrl());
    }

    public function onAuthAssignSuccess(ClientInterface $client)
    {
        if (!SettingHelper::isEnabledAuthClients()) {
            throw new ForbiddenHttpException('Access denied');
        }

        if ($client->getName() == 'google' && !SettingHelper::isEnabledGoogleAuthClient()) {
            throw new ForbiddenHttpException('Access denied');
        }

        if ($client->getName() == 'microsoft' && !SettingHelper::isEnabledMicrosoftAuthClient()) {
            throw new ForbiddenHttpException('Access denied');
        }

        $source = UserAuthClientSources::clientSourceFactory($client->getId());
        $source->handleAssign(Auth::id(), $this->action, $client);
        return $this->action->redirect($source->getRedirectUrl());
    }

    protected function redirectToTwoFactorAuth(Employee $user, LoginForm $model): Response
    {
//        $twoFactorAuthSecretKey = empty($user->userProfile->up_2fa_secret) ?
//            (new TwoFactorService())->getSecret() : $user->userProfile->up_2fa_secret;

        $session = Yii::$app->session;
        $session->set('two_factor_email', $user->email);
//        $session->set('two_factor_username', $user->username);
//        $session->set('two_factor_key_exist', !empty($user->userProfile->up_2fa_secret));
//        $session->set('two_factor_key', $twoFactorAuthSecretKey);
        $session->set('two_factor_remember_me', $model->rememberMe);
        return $this->redirect(['site/step-two']);
    }

    protected function get2FAAbacAccess($user): bool
    {
        return \Yii::$app->abac->can(null, TwoFactorAuthAbacObject::TWO_FACTOR_AUTH, TwoFactorAuthAbacObject::ACTION_ACCESS, $user);
    }
}
