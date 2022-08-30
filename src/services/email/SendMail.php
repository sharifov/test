<?php

namespace src\services\email;

use src\entities\email\EmailInterface;
use Yii;
use src\exception\EmailNotSentException;

abstract class SendMail
{
    abstract protected function generateContentData(EmailInterface $email);

    public function sendToCommunication(EmailInterface $email, array $contentData, array $data = [])
    {
        $templateType = $email->templateType;
        $tplType = $templateType ? $templateType->etp_key : null;
        $language = $email->getLanguageId() ?? 'en-US';
        $projectId = $email->getProjectId();

        $data['project_id'] = $projectId;

        $response = Yii::$app->comms->mailSend(
            $projectId,
            $tplType,
            $email->getEmailFrom(false),
            $email->getEmailTo(false),
            $contentData,
            $data,
            $language,
            0
        );

        if (isset($response['error']) && $response['error']) {
            $errorData = @json_decode($response['error'], true);
            $errorMessage = $errorData['message'] ?: $response['error'];
            throw new EmailNotSentException($email->getEmailTo(false), $errorMessage);
        }

        return $response['data'] ?? null;
    }
}
