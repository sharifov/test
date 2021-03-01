<?php

namespace modules\order\src\jobs;

use common\models\Email;
use common\models\EmailTemplateType;
use modules\order\src\entities\order\Order;
use yii\helpers\VarDumper;

class EmailConfirmationSender
{
    public function send(
        Order $order,
        $templateKey,
        $from,
        $fromName,
        $to,
        $toName,
        $languageId,
        $subject,
        $body
    ): void {
        $mail = new Email();
        $mail->e_project_id = $order->orLead->project_id;
        $mail->e_lead_id = $order->or_lead_id;
        $templateTypeId = EmailTemplateType::find()
            ->select(['etp_id'])
            ->andWhere(['etp_key' => $templateKey])
            ->asArray()
            ->one();
        if ($templateTypeId) {
            $mail->e_template_type_id = $templateTypeId['etp_id'];
        }
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $subject;
        $mail->body_html = $body;
        $mail->e_email_from = $from;
        $mail->e_email_from_name = $fromName;
        $mail->e_email_to_name = $toName;
        $mail->e_language_id = $languageId;
        $mail->e_email_to = $to;
        $mail->e_created_dt = date('Y-m-d H:i:s');

        if (!$mail->save()) {
            throw new \DomainException(VarDumper::dumpAsString($mail->getErrors()));
        }

        $mail->e_message_id = $mail->generateMessageId();
        $mail->save();
        $mailResponse = $mail->sendMail();

        if ($mailResponse['error'] !== false) {
            throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.');
        }
    }
}
