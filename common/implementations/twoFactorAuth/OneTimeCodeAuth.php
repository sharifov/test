<?php

namespace common\implementations\twoFactorAuth;

use common\models\UserConnection;
use kivork\TwoFactorAuth\Totp;
use src\model\user\entity\monitor\UserMonitor;

/**
 * Class OneTimeCodeAuth
 * @package common\implementations\twoFactorAuth
 */
class OneTimeCodeAuth extends Totp
{
    /**
     * @param \common\models\LoginForm $object
     * @return bool
     * @throws \Exception
     */
    public function auth($object): bool
    {
        if ($object->login()) {
            if (UserConnection::isIdleMonitorEnabled()) {
                UserMonitor::addEvent(\Yii::$app->user->id, UserMonitor::TYPE_LOGIN);
            }
            return true;
        }
        return false;
    }

    /**
     * @param \common\models\LoginForm $loginForm
     * @return null|string
     */
    public function getSecret($loginForm): ?string
    {
        return $loginForm->getUserSecret();
    }

    /**
     * @param \common\models\LoginForm $loginForm
     * @param $secret
     * @return bool
     */
    public function setSecret($loginForm, $secret): bool
    {
        return $loginForm->setUserSecret($secret);
    }

    /**
     * @param $user
     * @return string
     */
    public function getLabel($user): string
    {
        return 'TOTP Auth Of CRM';
    }

    /**
     * @param $user
     * @return string
     */
    public function getCompany($user): string
    {
        return 'KIVORK';
    }

    /**
     * @param $object
     * @return string
     */
    public function getAuthTypeName($object): string
    {
        return 'Auth By One-time Code';
    }
}
