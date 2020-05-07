<?php

namespace sales\model\email\useCase\send;

use common\models\Email;
use common\models\Quote;
use frontend\widgets\newWebPhone\email\form\EmailSendForm;
use Yii;
use yii\helpers\VarDumper;

class EmailSenderService
{
    public function send(EmailSendForm $form): array
    {
        $mail = new Email();
        $mail->e_project_id = $form->projectId;
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $form->subject;
        $mail->body_html = $previewEmailForm->e_email_message;
        $mail->e_email_from = $previewEmailForm->e_email_from;
        $mail->e_email_from_name = $previewEmailForm->e_email_from_name;
        $mail->e_email_to_name = $previewEmailForm->e_email_to_name;
        $mail->e_email_to = $previewEmailForm->e_email_to;
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_created_user_id = Yii::$app->user->id;

        if ($mail->save()) {

            $mail->e_message_id = $mail->generateMessageId();
            $mail->update();

            $previewEmailForm->is_send = true;

            $mailResponse = $mail->sendMail();

            if (isset($mailResponse['error']) && $mailResponse['error']) {
                //echo $mailResponse['error']; exit; //'Error: <strong>Email Message</strong> has not been sent to <strong>'.$mail->e_email_to.'</strong>'; exit;
                Yii::$app->session->setFlash('send-error', 'Error: <strong>Email Message</strong> has not been sent to <strong>' . $mail->e_email_to . '</strong>');
                Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n " . $mailResponse['error'], 'LeadController:view:Email:sendMail');
            } else {
                //echo '<strong>Email Message</strong> has been successfully sent to <strong>'.$mail->e_email_to.'</strong>'; exit;


                if ($quoteList = @json_decode($previewEmailForm->e_quote_list)) {
                    if (is_array($quoteList)) {
                        foreach ($quoteList as $quoteId) {
                            $quoteId = (int)$quoteId;
                            $quote = Quote::findOne($quoteId);
                            if ($quote) {
                                $quote->status = Quote::STATUS_SEND;
                                if (!$quote->save()) {
                                    Yii::error($quote->errors, 'LeadController:view:Email:Quote:save');
                                }
                            }
                        }
                    }
                }

                Yii::$app->session->setFlash('send-success', '<strong>Email Message</strong> has been successfully sent to <strong>' . $mail->e_email_to . '</strong>');
            }

            $this->refresh('#communication-form');

        } else {
            $previewEmailForm->addError('e_email_subject', VarDumper::dumpAsString($mail->errors));
            Yii::error(VarDumper::dumpAsString($mail->errors), 'LeadController:view:Email:save');
        }

        return $result;
    }
}
