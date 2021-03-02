<?php

namespace modules\order\src\services\confirmation;

use common\models\BillingInfo;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Lead;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\entities\order\Order;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\VarDumper;

class EmailConfirmationSender
{
    public function sendWithoutAttachments(Order $order): void
    {
        $this->send($order, []);
    }

    public function sendWithAttachments(Order $order): void
    {
        $invoice = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_INVOICE,
            'fo_or_id' => $order->or_id
        ])->one();
        if (!$invoice) {
            throw new \DomainException('Not found Invoice File. OrderId: ' . $order->or_id);
        }

        $countQuotes = count($order->productQuotes);
        $confirmations = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_CONFIRMATION,
            'fo_or_id' => $order->or_id
        ])->with(['file'])->all();

        if ($countQuotes !== count($confirmations)) {
            throw new \DomainException('Count Quotes(' . $countQuotes . ') and count Confirmation files(' . count($confirmations) . ') is not equal. OrderId: ' . $order->or_id);
        }

        $files = [];
        foreach ($confirmations as $confirmation) {
            $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                $confirmation->file->fs_name,
                $confirmation->file->fs_path,
                $confirmation->file->fs_uid,
                $confirmation->file->fs_title,
                null
            );
        }

        $fileStorageUrlGenerator = \Yii::createObject(\modules\fileStorage\src\services\url\UrlGenerator::class);
        $attachments['files'] = $fileStorageUrlGenerator->generateForExternal($files);

        $this->send($order, $attachments);
    }

    private function send(Order $order, array $files): void
    {
        $projectId = $order->orLead->project_id ?? null;

        if (!$projectId) {
            \Yii::error([
                'message' => 'Not found Project',
                'orderId' => $order->or_id,
            ], 'OrderCanceledConfirmationJob');
            return;
        }

        $project = $order->orLead->project;

        $from = $project->getContactInfo()->email;
        $fromName = $project->name;

        $billingInfo = BillingInfo::find()->select(['bi_id', 'bi_contact_email'])->andWhere(['bi_order_id' => $order->or_id])->orderBy(['bi_id' => SORT_DESC])->asArray()->one();
        if (!$billingInfo) {
            throw new \DomainException('Not found Billing Info. OrderId: ' . $order->or_id);
        }
        if (!$billingInfo['bi_contact_email']) {
            throw new \DomainException('Not found Billing Email. OrderId: ' . $order->or_id . ' BillingInfoId: ' . $billingInfo['bi_id']);
        }
        $to = $billingInfo['bi_contact_email'];

        $templateKey = 'bwk_multi_product';
        $languageId = $this->getLanguage($order->orLead);

        $mailPreview = \Yii::$app->communication->mailPreview(
            $projectId,
            $templateKey,
            $from,
            $to,
            (new EmailConfirmationData())->generate($order),
        );

        if ($mailPreview['error'] !== false) {
            throw new \DomainException($mailPreview['error']);
        }

        $this->sendEmail(
            $order,
            $templateKey,
            $from,
            $fromName,
            $to,
            $languageId,
            $mailPreview['data']['email_subject'],
            $mailPreview['data']['email_body_html'],
            $files
        );
    }

    private function getLanguage(Lead $lead): string
    {
        $locale = ProjectLocale::find()->select(['pl_language_id'])->andWhere([
            'pl_project_id' => $lead->project_id,
            'pl_default' => true
        ])->orderBy(['pl_id' => SORT_ASC])->asArray()->one();
        if ($locale) {
            return $locale['pl_language_id'];
        }
        return 'en-US';
    }

    private function sendEmail(
        Order $order,
        $templateKey,
        $from,
        $fromName,
        $to,
        $languageId,
        $subject,
        $body,
        array $files
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
        $mail->e_language_id = $languageId;
        $mail->e_email_to = $to;
        $mail->e_created_dt = date('Y-m-d H:i:s');
        if ($files) {
            $mail->e_email_data = json_encode($files);
        }

        if (!$mail->save()) {
            throw new \DomainException(VarDumper::dumpAsString($mail->getErrors()));
        }

        $mail->e_message_id = $mail->generateMessageId();
        $mail->save();
        $mailResponse = $mail->sendMail($files);

        if ($mailResponse['error'] !== false) {
            throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.');
        }
    }
}
