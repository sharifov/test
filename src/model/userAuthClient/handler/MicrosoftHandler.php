<?php

namespace src\model\userAuthClient\handler;

use common\components\TwoFactorService;
use common\models\Employee;
use common\models\LoginForm;
use common\models\Notifications;
use common\models\UserConnection;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\userAuthClient\entity\UserAuthClient;
use src\model\userAuthClient\entity\UserAuthClientRepository;
use src\model\userAuthClient\entity\UserAuthClientSources;
use src\model\user\entity\monitor\UserMonitor;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * @property-read UserAuthClientRepository $repository
 */
class MicrosoftHandler implements ClientHandler
{
    private UserAuthClientRepository $repository;

    private string $redirectUrl = '/site/login';

    public function __construct(UserAuthClientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AuthAction $authAction
     * @param ClientInterface $client
     * @return void
     * @throws \Exception
     */
    public function handle(AuthAction $authAction, ClientInterface $client): void
    {
        $userAttributes = $client->getUserAttributes();

        $sourceId = ArrayHelper::getValue($userAttributes, 'id');
        $source = UserAuthClientSources::getIdByValue($client->getId());
        $authClients = UserAuthClient::find()->with('user')->where([
            'uac_source' => $source,
            'uac_source_id' => $sourceId
        ])->all();

        $countAuthClients = count($authClients);

        /**
         * NOTICE!
         * The condition `$countAuthClients > 1` never be true, because `uac_source` & `uac_source_id` pair is unique
         * for `user_auth_client` table. Key: `UNQ-user_auth_client`
         * This restriction has been added in `console/migrations/archive/2022/m220106_114232_add_unique_index_for_user_auth_client.php`.
         *
         * I think this condition should be deleted.
         */
        if ($countAuthClients > 1) {
            $this->redirectToStepTwo($authAction, $source, $sourceId);
        } elseif ($countAuthClients === 1 && $authClients[0]) {
            $this->login($authClients[0]->user);
        } else {
            $email = ArrayHelper::getValue($userAttributes, 'mail');

            $user = Employee::findOne(['email' => $email]);
            if ($user) {
                $authClient = UserAuthClient::create($user->id, $sourceId, $email, \Yii::$app->request->remoteIP, \Yii::$app->request->userAgent);
                $authClient->setMicrosoftSource();
                try {
                    $this->repository->save($authClient);
                    $this->login($user);
                    Notifications::create($user->id, 'Auth client assigned', 'Source: ' . UserAuthClientSources::getName($authClient->uac_source) . ' was assigned to your account.', Notifications::TYPE_INFO, true);
                } catch (\RuntimeException $e) {
                    \Yii::warning(AppHelper::throwableLog($e), 'auth:MicrosoftHandler:handle:RuntimeException');
                    \Yii::$app->session->setFlash('error', 'Login failed');
                }
            } else {
                \Yii::$app->session->setFlash('error', 'User with email: ' . $email . ' is not registered');
            }
        }
    }

    /**
     * @param int $userId
     * @param AuthAction $authAction
     * @param ClientInterface $client
     * @throws \Exception
     */
    public function handleAssign(int $userId, AuthAction $authAction, ClientInterface $client): void
    {
        $userAttributes = $client->getUserAttributes();
        $sourceId = ArrayHelper::getValue($userAttributes, 'id');
        $source = UserAuthClientSources::getIdByValue($client->getId());
        $authClients = UserAuthClient::find()->with('user')->where([
            'uac_user_id' => $userId,
            'uac_source' => $source,
            'uac_source_id' => $sourceId
        ])->one();
        $email = ArrayHelper::getValue($userAttributes, 'mail');
        if (!$authClients) {
            $authClient = UserAuthClient::create($userId, $sourceId, $email, \Yii::$app->request->remoteIP, \Yii::$app->request->userAgent);
            $authClient->setMicrosoftSource();
            try {
                $this->repository->save($authClient);
                Notifications::create($userId, 'Auth client assigned', 'Source: ' . UserAuthClientSources::getName($authClient->uac_source) . ' was assigned to your account.', Notifications::TYPE_INFO, true);
                \Yii::$app->session->setFlash('success', 'User with email: ' . $email . ' successfully assigned to your profile');
            } catch (\RuntimeException $e) {
                \Yii::warning(AppHelper::throwableLog($e), 'auth:MicrosoftHandler:handleAssign:RuntimeException');
                \Yii::$app->session->setFlash('error', 'Assigning client failed');
            }
        } else {
            \Yii::$app->session->setFlash('warning', 'User with email: ' . $email . ' already assigned to your profile');
        }
        $this->setRedirectUrl('/site/profile');
    }

    protected function redirectToStepTwo(AuthAction $authAction, int $source, string $sourceId): void
    {
        $session = \Yii::$app->session;
        $session->set('auth_client_source', $source);
        $session->set('auth_client_source_id', $sourceId);
        $this->setRedirectUrl('/site/auth-step-two');
    }

    protected function login(Employee $user): void
    {
        $form = new LoginForm();
        $form->setUser($user);
        $form->setUserChecked(true);
        if (SettingHelper::isTwoFactorAuthEnabled() && $user->userProfile && $user->userProfile->is2faEnable()) {
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_TWO_FACTOR_AUTH_MODULE)) {
                $module = \Yii::$app->getModule('two-factor-auth');
                $url = $module->startAuthProcess($form, false);
                $this->setRedirectUrl($url);
            } else {
                $this->redirectToTwoFactorAuth($user, $form);
            }
        } elseif ($form->login()) {
            if (UserConnection::isIdleMonitorEnabled()) {
                UserMonitor::addEvent($user->id, UserMonitor::TYPE_LOGIN);
            }
            $this->setRedirectUrl(\Yii::$app->getUser()->getReturnUrl());
        }
    }

    protected function setRedirectUrl(string $url): void
    {
        $this->redirectUrl = $url;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    protected function redirectToTwoFactorAuth(Employee $user, LoginForm $model): void
    {
        $twoFactorAuthSecretKey = empty($user->userProfile->up_2fa_secret) ?
            (new TwoFactorService())->getSecret() : $user->userProfile->up_2fa_secret;

        $session = \Yii::$app->session;
        $session->set('two_factor_email', $user->email);
        $session->set('two_factor_username', $user->username);
        $session->set('two_factor_key_exist', !empty($user->userProfile->up_2fa_secret));
        $session->set('two_factor_key', $twoFactorAuthSecretKey);
        $session->set('two_factor_remember_me', $model->rememberMe);
        $this->setRedirectUrl('/site/step-two');
    }
}
