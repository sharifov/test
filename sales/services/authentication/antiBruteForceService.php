<?php

namespace sales\services\authentication;

use frontend\models\UserFailedLogin;
use Yii;

/**
 * Class antiBruteForceService
 */
class antiBruteForceService
{
    private bool $captchaLoginEnable;
    private int $captchaLoginAttempts;
    private int $userNotifyFailedLoginAttempts;
    private int $userBlockAttempts;
    private string $ip;

    /**
     * antiBruteForceService constructor.
     * @param bool|null $captchaLoginEnable
     * @param int|null $captchaLoginAttempts
     * @param int|null $userNotifyFailedLoginAttempts
     * @param int|null $userBlockAttempts
     */
    public function __construct(
        bool $captchaLoginEnable = null,
        int $captchaLoginAttempts = null,
        int $userNotifyFailedLoginAttempts = null,
        int $userBlockAttempts = null
    ) {
        $settings = Yii::$app->params['settings'];
        $this->captchaLoginEnable = ($captchaLoginEnable !== null) ? $captchaLoginEnable : $settings['captcha_login_enable'];
        $this->captchaLoginAttempts = ($captchaLoginAttempts !== null) ? $captchaLoginAttempts : $settings['captcha_login_attempts'];
        $this->captchaLoginAttempts = ($userNotifyFailedLoginAttempts !== null) ? $userNotifyFailedLoginAttempts : $settings['captcha_login_attempts'];
        $this->captchaLoginAttempts = ($captchaLoginAttempts !== null) ? $captchaLoginAttempts : $settings['captcha_login_attempts'];
        $this->ip = self::getClientIPAddress();
    }

    /**
     * @return string
     */
    public static function getClientIPAddress(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
    }

    /**
     * @return bool
     */
    public function checkCaptchaEnable(): bool
    {
        if ($this->captchaLoginEnable) {
            $failedLoginCount = UserFailedLogin::getCountActiveByIp($this->ip);
            if ($this->captchaLoginAttempts === 0 || $failedLoginCount >= $this->captchaLoginAttempts) {
                return true;
            }
        }
        return false;
    }


}