<?php

namespace src\useCase\login\twoFactorAuth\service;

use common\components\TwoFactorService;
use Yii;

class TotpAuthService
{
    private const SECRET_KEY = 'totp_secret_key';

    public function getOrSetSecretAuthKey(\common\models\Employee $user)
    {
        $secret = Yii::$app->session->get(self::SECRET_KEY);
        if (empty($secret)) {
            $secret = empty($user->userProfile->up_2fa_secret) ?
                (new TwoFactorService())->getSecret() : $user->userProfile->up_2fa_secret;
            Yii::$app->session->set('totp_secret_key', $secret);
        }
        return $secret;
    }

    public function removeSecretAuthKey(): void
    {
        Yii::$app->session->remove(self::SECRET_KEY);
    }
}
