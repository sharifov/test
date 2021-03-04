<?php

namespace modules\order\src\services\confirmation;

use common\models\BillingInfo;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Lead;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\VarDumper;

class EmailConfirmationSender
{
    private const TEMPLATE = 'order_update';

    public function sendWithoutAttachments(Order $order): void
    {
        $this->send($order, []);
    }

    public function sendWithAllAttachments(Order $order): void
    {
        $receipt = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_RECEIPT,
            'fo_or_id' => $order->or_id
        ])->one();
        if (!$receipt) {
            throw new \DomainException('Not found Receipt File. OrderId: ' . $order->or_id);
        }

        /** @var FileOrder[] $confirmations */
        $confirmations = [];

        $quotes = ProductQuote::find()->select(['pq_id'])->andWhere(['pq_order_id' => $order->or_id])->column();
        foreach ($quotes as $quote) {
            $confirm = FileOrder::find()->andWhere([
                'fo_category_id' => FileOrder::CATEGORY_CONFIRMATION,
                'fo_or_id' => $order->or_id,
                'fo_pq_id' => $quote
            ])->with(['file'])->one();

            if (!$confirm) {
                throw new \DomainException('Not found File Confirmation. QuoteId: ' . $quote . ' OrderId: ' . $order->or_id);
            }
            $confirmations[] = $confirm;
        }

        $files[] = new \modules\fileStorage\src\services\url\FileInfo(
            $receipt->file->fs_name,
            $receipt->file->fs_path,
            $receipt->file->fs_uid,
            $receipt->file->fs_title,
            null
        );

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

    public function sendWithAnyAttachments(Order $order): void
    {
        $files = [];

        $receipt = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_RECEIPT,
            'fo_or_id' => $order->or_id
        ])->one();

        if ($receipt) {
            $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                $receipt->file->fs_name,
                $receipt->file->fs_path,
                $receipt->file->fs_uid,
                $receipt->file->fs_title,
                null
            );
        }

        /** @var FileOrder[] $confirmations */
        $confirmations = [];

        $quotes = ProductQuote::find()->select(['pq_id'])->andWhere(['pq_order_id' => $order->or_id])->column();
        foreach ($quotes as $quote) {
            $confirm = FileOrder::find()->andWhere([
                'fo_category_id' => FileOrder::CATEGORY_CONFIRMATION,
                'fo_or_id' => $order->or_id,
                'fo_pq_id' => $quote
            ])->with(['file'])->one();

            if (!$confirm) {
                continue;
            }
            $confirmations[] = $confirm;
        }

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

        $billingInfo = BillingInfo::find()->select([
            'bi_id',
            'bi_contact_email'
        ])->andWhere(['bi_order_id' => $order->or_id])->orderBy(['bi_id' => SORT_DESC])->asArray()->one();
        if (!$billingInfo) {
            throw new \DomainException('Not found Billing Info. OrderId: ' . $order->or_id);
        }
        if (!$billingInfo['bi_contact_email']) {
            throw new \DomainException('Not found Billing Email. OrderId: ' . $order->or_id . ' BillingInfoId: ' . $billingInfo['bi_id']);
        }
        $to = $billingInfo['bi_contact_email'];

        $languageId = $this->getLanguage($order->orLead);

        $mailPreview = \Yii::$app->communication->mailPreview(
            $projectId,
            self::TEMPLATE,
            $from,
            $to,
            (new EmailConfirmationData())->generate($order),
        );

        if ($mailPreview['error'] !== false) {
            throw new \DomainException($mailPreview['error']);
        }

        $this->sendEmail(
            $order,
            self::TEMPLATE,
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
        if ($lead->l_client_lang) {
            return $lead->l_client_lang;
        }
        $locale = ProjectLocale::find()->select(['pl_language_id'])->andWhere([
            'pl_project_id' => $lead->project_id,
            'pl_default' => true
        ])->orderBy(['pl_id' => SORT_ASC])->asArray()->one();
        if ($locale && $locale['pl_language_id']) {
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