<?php

namespace src\model\email\useCase\send;

use common\models\Email;
use frontend\widgets\newWebPhone\email\form\EmailSendForm;
use Yii;
use yii\helpers\VarDumper;

/**
 *
 * @deprecated
 * use EmailMainService instead
 *
 */
class EmailSenderService
{
    public function send(EmailSendForm $form): array
    {
        $mail = new Email();
        $mail->e_project_id = $form->getProjectId();
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $form->subject;
        $mail->body_html = $form->text;
        $mail->e_email_from = $form->userEmail;
        $mail->e_email_from_name = $form->user->full_name;
        $mail->e_email_to_name = $form->getContactName();
        $mail->e_email_to = $form->getContactEmail();
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_created_user_id = $form->user->id;

        $transaction = Yii::$app->db->beginTransaction();

        if ($mail->save()) {
            $mail->e_message_id = $mail->generateMessageId();
            $mail->update();
            $mailResponse = $mail->sendMail();

            if (!empty($mailResponse['error'])) {
                $error = $mailResponse['error'];
                $result['errors'] = ['communication' => [$error]];
                Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n" . $error, 'EmailSenderService:send');
            } else {
                $result['success'] = true;
            }
        } else {
            Yii::error(VarDumper::dumpAsString($mail->errors), 'EmailSenderService:send');
            $result['errors'] = $mail->getErrors();
        }

        if (isset($result['success'])) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $result;
    }
}
