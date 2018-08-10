<?php
namespace common\components;

use common\models\Project;
use Yii;
use yii\base\Exception;

class EmailService
{
    /**
     * @param string $to
     * @param Project $source
     * @param $credential
     * @param string $subject
     * @param string $body
     * @param array $errors
     * @param array $bcc
     * @return bool
     */
    public static function send($to, Project $source, $credential, $subject, $body, &$errors = [], $bcc = [])
    {
        try {
            $transporter = new \Swift_SmtpTransport(
                $source->contactInfo->smtpHost,
                $source->contactInfo->smtpPort,
                $source->contactInfo->encryption
            );
            $transporter->setUsername($credential['email']);
            $transporter->setPassword($credential['password']);


            $mailer = new \Swift_Mailer($transporter);

            $mEmail = new \Swift_Message($subject);

            $mEmail->setSubject($subject);
            $mEmail->setTo($to);
            if (!empty($bcc)) {
                $mEmail->setBcc($bcc);
            }
            if (!empty($bcc)) {
                $mEmail->setBcc($bcc);
            }
            $mEmail->setFrom(array($credential['email'] => strtoupper($source->name)));
            $mEmail->setContentType('text/plain; charset=UTF-8');
            $mEmail->setBody($subject, 'text/plain');
            $mEmail->addPart($body, 'text/html');

            $failedRecipients = [];
            if ($mailer->send($mEmail, $failedRecipients)) {
                return true;
            } else {
                $errors = $failedRecipients;
                Yii::warning(sprintf("Send error for:\n%s", print_r($failedRecipients, true)), 'EmailService->send()');
            }
        } catch (\Swift_SwiftException $ex) {
            $errors[] = $ex->getMessage();
            Yii::warning(sprintf("Send error:\n%s\n\n%s",$ex->getMessage(), print_r($ex->getTraceAsString(), true)), 'EmailService->send()');
        }
        return false;
    }

    /**
     * @param Project $source
     * @return \Swift_Signers_DKIMSigner
     */
    private static function getDKIMSigner(Project $source)
    {
        $patterns = [
            '/https:\/\//', '/http:\/\//', '/www./'
        ];
        $replacements = ['', '', ''];
        $domain = preg_replace($patterns, $replacements, $source->link);
        $path = sprintf('/etc/ssl/dkim/%s/key.priv', explode('.', $domain)[0]);
        return new \Swift_Signers_DKIMSigner(file_get_contents($path), $domain, 'default');
    }
}