<?php

namespace common\components\email;

use common\components\email\dto\EmailDto;
use src\helpers\app\AppHelper;
use yii\base\Component;
use yii\base\InvalidArgumentException;

class EmailComponent extends Component
{
    public ?string $defaultFromEmail = null;

    public function init()
    {
        parent::init();

        if (empty($this->defaultFromEmail)) {
            throw new InvalidArgumentException('Default From email is not set');
        }
    }

    /**
     * @param EmailDto $emailData
     * @return bool
     */
    public function send(EmailDto $emailData): bool
    {
        if (!empty($swiftMailer = \Yii::$app->mailer)) {
            try {
                $isSend = $swiftMailer
                    ->compose()
                    ->setTo($emailData->to)
                    ->setFrom($emailData->from ?: $this->defaultFromEmail)
                    ->setSubject($emailData->title)
                    ->setHtmlBody($emailData->body)
                    ->send();
                if ($isSend) {
                    return true;
                }
                \Yii::warning("Email " . $emailData->to . " send failed", 'EmailComponent:send');
            } catch (\Throwable $ex) {
                \Yii::error(AppHelper::throwableLog($ex, true), 'EmailComponent:send');
            }
        } else {
            \Yii::warning("Component mailer doesn't exist", 'EmailComponent:send');
        }

        return false;
    }
}
