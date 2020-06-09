<?php

namespace sales\services\authentication;

use common\models\Email;
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
                $this->setNotificationTitle($user);
                $this->setNotificationMessage($user);
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
                $this->setMessageForBlocked($user, false);
                foreach ($admins as $admin) {
                    $this->sendNotification($admin);
                    $this->sendEmail($admin);
                }
            }
        }
    }

    /**
     * @param Employee $user
     * @param string|null $title
     * @return string
     */
    private function setNotificationTitle(Employee $user, ?string $title = null): string
    {
        $title = $title ?: 'Attention. Failed authentication attempt by user : %s, id : %d ';
        $this->notificationTitle = sprintf($title, $user->username, $user->getId());
        return $this->notificationTitle;
    }

    /**
     * @param Employee $user
     * @param int|null $limit
     * @param string|null $body
     * @return string
     */
    private function setNotificationMessage(Employee $user, ?int $limit = null, ?string $body = null): string
    {
        $limit = $limit ?: $this->userNotifyFailedLoginAttempts;
        $body = $body ?: 'The limit: %d of failed authentication attempts has been reached by user: %s, id: %s ';
        $this->notificationMessage = sprintf($body, $limit, $user->username, $user->getId());
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
        $this->notificationTitle = sprintf('Account is blocked. Username: "%s" (%d)', $user->username, $user->id);
        return $this->notificationTitle;
    }

    /**
     * @param Employee $user
     * @param bool $info
     * @return string
     */
    public function setMessageForBlocked(Employee $user, bool $info = true): string
    {
        $this->notificationMessage = 'Account is blocked. Username: "' . $user->username . '" (' . $user->id . ')';
        if ($info) {
            $this->notificationMessage .= "\n\nLast failed attempts: \n\n";
            foreach (UserFailedLogin::getLastAttempts($user->id) as $value) {
                $this->notificationMessage .= '- DateTime: ' . $value->ufl_created_dt .', IP: ' . $value->ufl_ip . ', UserAgent: ' . $value->ufl_ua . "\n";
            }
        }
        return $this->notificationMessage;
    }
}