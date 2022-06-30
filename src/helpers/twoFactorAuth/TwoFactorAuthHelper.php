<?php

namespace src\helpers\twoFactorAuth;

use src\helpers\setting\SettingHelper;
use Yii;

class TwoFactorAuthHelper
{
    public const AUTH_ATTEMPTS_KEY = 'auth_attempts';

    /**
     * @return int
     */
    public static function getAuthAttempts(): int
    {
        if (is_null($attempts = Yii::$app->session->get(self::AUTH_ATTEMPTS_KEY))) {
            $attempts = SettingHelper::getTwoFactorAuthMaxAttempts();
            self::setAuthAttempts($attempts);
        }
        return (int) $attempts;
    }

    /**
     * @param $value
     * @return void
     */
    public static function setAuthAttempts($value): void
    {
        Yii::$app->session->set(self::AUTH_ATTEMPTS_KEY, $value);
    }

    /**
     * @return void
     */
    public static function removeAuthAttempts(): void
    {
        Yii::$app->session->remove(self::AUTH_ATTEMPTS_KEY);
    }

    /**
     * @return bool
     */
    public static function showWarningAttemptsRemain(): bool
    {
        return (SettingHelper::getTwoFactorAuthWarningAttemptsRemain() - self::getAuthAttempts()) >= 0;
    }
}
