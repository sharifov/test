<?php

namespace sales\services\authentication;

use common\models\Employee;
use common\models\Notifications;
use frontend\models\UserFailedLogin;
use frontend\widgets\notification\NotificationMessage;
use sales\helpers\app\AppHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class antiBruteForceService
 */
class AntiBruteForceService
{
    public string $notificationTitle;
    public string $notificationMessage;

    public bool $captchaLoginEnable;
    public int $captchaLoginAttempts;
    public int $userNotifyFailedLoginAttempts;
    public int $userBlockAttempts;
    public bool $internalNotificationForUserEmail;

    public string $ip;
    private Employee $user;

    /**
     * AntiBruteForceService constructor.
     */
    public function __construct() {
        $settings = Yii::$app->params['settings'];
        $this->captchaLoginEnable = $settings['captcha_login_enable'];
        $this->captchaLoginAttempts = $settings['captcha_login_attempts'];
        $this->userNotifyFailedLoginAttempts = $settings['user_notify_failed_login_attempts'];
        $this->userBlockAttempts = $settings['user_block_attempts'];
        $this->internalNotificationForUserEmail = $settings['internal_notification_for_user_email'];
        $this->ip = AntiBruteForceHelper::getClientIPAddress();
    }

    /**
     * @return bool
     */
    public function checkCaptchaEnable(): bool
    {
        if ($this->captchaLoginEnable) {
            $failedLoginCount = (new UserFailedLogin())->getCountActiveByIp($this->ip);
            if ($this->captchaLoginAttempts === 0 || $failedLoginCount >= $this->captchaLoginAttempts) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Employee $user
     */
    public function checkAttempts(Employee $user): void
    {
        if (!$user->isBlocked() && $attempts = (new UserFailedLogin())->getCountActiveByUserId($user->id)) {
            if ($this->userNotifyFailedLoginAttempts !== 0 &&
                $attempts >= $this->userNotifyFailedLoginAttempts &&
                $attempts < $this->userBlockAttempts
            ) {
                $this->notificationTitle = 'Review failed sign-in attempt';
                $this->setUserNotificationMessage($user);
                $this->sendNotification($user);
                $this->sendEmail($user);

            } elseif ($this->userBlockAttempts !== 0 && $attempts >= $this->userBlockAttempts) {
                $user->setBlocked();
                if (!$user->save(false)) {
                    \Yii::error(VarDumper::dumpAsString($user->getErrors(), 10),
                    'antiBruteForceService:checkAttempts:saveFailed');
                }

                $this->setTitleForBlocked($user);
                $this->setMessageForBlocked($user);
                $this->sendNotification($user);
                $this->sendEmail($user);

                $admins = Employee::getAllEmployeesByRole(Employee::ROLE_ADMIN);
                $this->setMessageForBlocked($user);
                foreach ($admins as $admin) {
                    $this->sendNotification($admin);
                    $this->sendEmail($admin);
                }
            }
        }
    }

    /**
     * @param Employee $user
     * @return string
     */
    private function setUserNotificationMessage(Employee $user): string
    {
        $this->notificationMessage = "Sing-in attempts limit has been reached. If this wasn't you, please contact administrator. \n\n";
        $this->notificationMessage .= "Last failed attempts: \n";
        foreach (UserFailedLogin::getLastAttempts($user->id) as $value) {
            $browser = explode('UserAgent', $value->ufl_ua);
            $this->notificationMessage .= '[Failed] ' . $value->ufl_created_dt .' - IP: ' . $value->ufl_ip . ' / ' . $browser[0] . "\n";
        }
        return $this->notificationMessage;
    }

    /**
     * @param Employee $user
     * @return bool
     */
    private function sendNotification(Employee $user): bool
    {
        $notification = Notifications::create(
            $user->id,
            $this->notificationTitle,
            $this->notificationMessage,
            Notifications::TYPE_WARNING,
            true
        );

        if ($notification) {
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($notification) : [];
            return Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
        }
        return false;
    }

    /**
     * @param Employee $user
     * @return bool
     */
    private function sendEmail(Employee $user): bool
    {
		if ($this->internalNotificationForUserEmail === false) {
		    return false;
		}

		try {
		    Yii::$app->mailer->compose()
                ->setFrom(\Yii::$app->params['email_from']['no-reply'])
                ->setTo($user->email)
                ->setSubject($this->notificationTitle)
                ->setTextBody($this->notificationMessage)
                ->setHtmlBody($this->notificationMessage)
                ->send();
		} catch (\Throwable $throwable) {
		    Yii::error(AppHelper::throwableFormatter($throwable), 'antiBruteForceService:sendEmail:failed');
		    return false;
		}
		return true;
    }

    /**
     * @param Employee $user
     * @return string
     */
    public function setTitleForBlocked(Employee $user): string
    {
        $this->notificationTitle = sprintf('Blocked account. User: "%s"', $user->username);
        return $this->notificationTitle;
    }

    /**
     * @param Employee $user
     * @return string
     */
    public function setMessageForBlocked(Employee $user): string
    {
        $this->notificationMessage = "The system has blocked user account. Reason: login attempts limit reached.\n";
        $this->notificationMessage .= 'Username: "' . $user->username . '" (' . $user->id . ')';
        return $this->notificationMessage;
    }
}