<?php

namespace common\components\email;

use common\models\Employee;
use Swift_TransportException;
use yii\helpers\VarDumper;

class EmailService
{
    private EmailComponent $emailComponent;

    /**
     * @param EmailComponent $emailComponent
     */
    public function __construct(EmailComponent $emailComponent)
    {
        $this->emailComponent = $emailComponent;
    }

    /**
     * @param string $code
     * @param Employee $user
     * @return bool
     */
    public function sendEmailCodeVerification(string $code, Employee $user): bool
    {
        $emailData = [
            'code' => $code,
            'to' => $user->email,
            'from' => $this->emailComponent->getEmailFrom(),
            'title' => 'Code verification',
            'template' => 'verificationCode-text',
            'templateType' => 'text',
            'templateParams' => [
                'code' => $code,
            ],
        ];

        return $this->invokeSend($emailData);
    }

    /**
     * @param array $emailData
     * @return bool
     */
    public function invokeSend(array $emailData): bool
    {
        if ($this->emailComponent->isActive && !empty($swiftMailer = \Yii::$app->mailer)) {
            try {
                if (!empty($emailData['template'])) {
                    $isSend = $swiftMailer->compose([
                        ($emailData['templateType'] ?? 'text') => $emailData['template']
                    ], $emailData['templateParams'] ?? []);
                } else {
                    $isSend = $swiftMailer
                        ->compose()
                        ->setHtmlBody($emailData['body'] ?? '');
                }

                $isSend
                    ->setTo($emailData['to'])
                    ->setFrom($emailData['from'])
                    ->setSubject($emailData['title'])
                    ->send();

                if ($isSend) {
                    return true;
                }
                \Yii::warning(sprintf("Email '%s' send failed", $emailData['to']), 'EmailService->invokeSend()');
            } catch (Swift_TransportException $ex) {
                \Yii::error(print_r($ex, true), 'EmailService->invokeSend()');
            }
        }
        return false;
    }
}
