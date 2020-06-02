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

    private bool $captchaLoginEnable;
    private int $captchaLoginAttempts;
    private int $userNotifyFailedLoginAttempts;
    private int $userBlockAttempts;
    private string $ip;
    private Employee $user;

    /**
     * antiBruteForceService constructor.
     * @param bool|null $captchaLoginEnable
     * @param int|null $captchaLoginAttempts
     * @param int|null $userNotifyFailedLoginAttempts
     * @param int|null $userBlockAttempts
     */
    public function __construct(
        ?bool $captchaLoginEnable = null,
        ?int $captchaLoginAttempts = null,
        ?int $userNotifyFailedLoginAttempts = null,
        ?int $userBlockAttempts = null
    ) {
        $settings = Yii::$app->params['settings'];
        $this->captchaLoginEnable = ($captchaLoginEnable !== null) ? $captchaLoginEnable : $settings['captcha_login_enable'];
        $this->captchaLoginAttempts = ($captchaLoginAttempts !== null) ? $captchaLoginAttempts : $settings['captcha_login_attempts'];
        $this->userNotifyFailedLoginAttempts = ($userNotifyFailedLoginAttempts !== null) ?
            $userNotifyFailedLoginAttempts : $settings['user_notify_failed_login_attempts'];
        $this->userBlockAttempts = ($userBlockAttempts !== null) ? $userBlockAttempts : $settings['user_block_attempts'];
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

    /**
     * @param Employee $user
     */
    public function checkAttempts(Employee $user): void
    {
        if (!$user->isBlocked() && $attempts = UserFailedLogin::getCountActiveByUserId($user->id)) {
            if ($attempts >= $this->userNotifyFailedLoginAttempts &&
                $attempts < $this->userBlockAttempts
            ) {
                $this->setNotificationTitle($user);
                $this->setNotificationMessage($user);
                $this->sendNotification($user);
                $this->sendEmail($user);

            } elseif ($attempts >= $this->userBlockAttempts) {
                $user->setBlocked();
                if (!$user->save()) {
                    \Yii::error(VarDumper::dumpAsString($user->getErrors(), 10),
                    'antiBruteForceService:checkAttempts:saveFailed');
                }

                $this->setTitleForBlocked($user);
                $this->setMessageForBlocked($user);
                $this->sendNotification($user);
                $this->sendEmail($user);

                $admins = Employee::getAllEmployeesByRole(Employee::ROLE_ADMIN);;
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
        $title = $title ?? 'Attention. Failed authentication attempt by user : %s, id : %d';
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
        $limit = $limit ?? $this->userNotifyFailedLoginAttempts;
        $body = $body ?? 'The limit: %d of failed authentication attempts has been reached by user: %s, id: %s';
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
		    $email = new Email();
            $email->e_type_id = Email::TYPE_OUTBOX;
            $email->e_status_id = Email::STATUS_PENDING;
            $email->e_email_subject = $this->notificationTitle;
            $email->body_html = $this->notificationMessage;
            $email->e_email_from = $user->email;
            $email->e_email_to = $user->email;

            if (!$email->save()) {
                throw new \RuntimeException($email->getErrorSummary(false)[0]);
            }
            $email->e_message_id = $email->generateMessageId();
            $email->update();

            //$mailResponse = $email->sendMail(); /* TODO::  */
            /*if (isset($mailResponse['error']) && $mailResponse['error']) {
                throw new \RuntimeException(VarDumper::dumpAsString($mailResponse['error'], 10));
            }*/
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
        $this->notificationTitle = sprintf('Account is blocked. User : %s, id : %d', $user->username, $user->getId());
        return $this->notificationTitle;
    }

    /**
     * @param Employee $user
     * @param bool $info
     * @return string
     */
    public function setMessageForBlocked(Employee $user, bool $info = false): string
    {
        $this->notificationMessage = "Account is blocked. \n";
        $this->notificationMessage .= $this->setNotificationMessage($user) . "\n\n";

        if ($info) {
            foreach (UserFailedLogin::getLastAttempts($user->id) as $value) {
                /** @var UserFailedLogin $value */
                $this->notificationMessage .= 'Date : ' . $value->ufl_created_dt .
                ' IP : ' . $value->ufl_ip .
                ' User Agent : ' . $value->ufl_ua . "\n";
            }
        }
        return $this->notificationMessage;
    }
}