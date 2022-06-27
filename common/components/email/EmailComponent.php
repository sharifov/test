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

        if (empty(\Yii::$app->mailer)) {
            throw new \RuntimeException("Component mailer doesn't exist");
        }

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
        try {
            $isSend = \Yii::$app->mailer
                ->compose()
                ->setTo($emailData->to)
                ->setFrom($emailData->from ?: $this->defaultFromEmail)
                ->setSubject($emailData->title)
                ->setHtmlBody($emailData->body)
                ->send();
            if (!$isSend) {
                throw new \RuntimeException("Email " . $emailData->to . " send failed", 'EmailComponent:send');
            }
            return true;
        } catch (\RuntimeException $ex) {
            \Yii::warning(AppHelper::throwableLog($ex), 'EmailComponent:send');
        } catch (\Throwable $ex) {
            \Yii::error(AppHelper::throwableLog($ex, true), 'EmailComponent:send');
        }
        return false;
    }
}
