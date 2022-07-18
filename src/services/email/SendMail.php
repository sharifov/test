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

        $request = Yii::$app->comms->mailSend(
            $projectId,
            $tplType,
            $email->getEmailFrom(false),
            $email->getEmailTo(false),
            $contentData,
            $data,
            $language,
            0
        );

        if (isset($request['error']) && $request['error']) {
            $errorData = @json_decode($request['error'], true);
            $errorMessage = $errorData['message'] ?: $request['error'];
            throw new EmailNotSentException($email->e_email_to, $errorMessage);
        }

        return $request['data'] ?? null;
    }
}
