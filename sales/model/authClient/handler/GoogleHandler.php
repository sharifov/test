<?php

namespace sales\model\authClient\handler;

use common\models\Employee;
use common\models\LoginForm;
use common\models\UserConnection;
use Da\TwoFA\Manager;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\authClient\entity\AuthClient;
use sales\model\authClient\entity\AuthClientRepository;
use sales\model\authClient\entity\AuthClientSources;
use sales\model\user\entity\monitor\UserMonitor;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * @property-read AuthClientRepository $repository
 */
class GoogleHandler implements ClientHandler
{
    private AuthClientRepository $repository;

    private string $redirectUrl = '/site/login';

    public function __construct(AuthClientRepository $repository)
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
        $source = AuthClientSources::getIdByValue($client->getId());
        $authClients = AuthClient::find()->with('user')->where([
            'ac_source' => $source,
            'ac_source_id' => $sourceId
        ])->all();

        $countAuthClients = count($authClients);

        if ($countAuthClients > 1) {
            $this->redirectToStepTwo($authAction, $source, $sourceId);
        } elseif ($countAuthClients === 1 && $authClients[0]) {
            $this->login($authClients[0]->user);
        } else {
            $email = ArrayHelper::getValue($client->getUserAttributes(), 'email');

            $user = Employee::findOne(['email' => $email]);
            if ($user) {
                $authClient = AuthClient::create(
                    $user->id,
                    $sourceId,
                    $email,
                    \Yii::$app->request->remoteIP,
                    \Yii::$app->request->userAgent
                );
                $authClient->setGoogleSource();
                try {
                    $this->repository->save($authClient);
                    $this->login($user);
                } catch (\RuntimeException $e) {
                    \Yii::warning(AppHelper::throwableLog($e), 'auth:GoogleHandler:RuntimeException');
                    \Yii::$app->session->setFlash('error', 'Login failed');
                }
            } else {
                \Yii::$app->session->setFlash('error', 'User with email: ' . $email . ' is not registered');
            }
        }
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
            $this->redirectToTwoFactorAuth($user, $form);
        } elseif ($form->login()) {
            if (UserConnection::isIdleMonitorEnabled()) {
                UserMonitor::addEvent($user->id, UserMonitor::TYPE_LOGIN);
            }
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
        $twoFaManager = new Manager();
        $twoFaManager->setCounter(SettingHelper::getTwoFactorAuthCounter());
        $twoFactorAuthSecretKey = empty($user->userProfile->up_2fa_secret) ?
            $twoFaManager->generateSecretKey() : $user->userProfile->up_2fa_secret;

        $session = \Yii::$app->session;
        $session->set('two_factor_email', $user->email);
        $session->set('two_factor_username', $user->username);
        $session->set('two_factor_key_exist', !empty($user->userProfile->up_2fa_secret));
        $session->set('two_factor_key', $twoFactorAuthSecretKey);
        $session->set('two_factor_remember_me', $model->rememberMe);
        $this->setRedirectUrl('/site/step-two');
    }
}
