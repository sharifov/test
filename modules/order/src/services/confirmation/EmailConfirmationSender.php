<?php

namespace modules\order\src\services\confirmation;

use common\models\BillingInfo;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Lead;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileProductQuote\FileProductQuote;
use modules\fileStorage\src\entity\fileProductQuote\FileProductQuoteQuery;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderEmail\OrderEmail;
use modules\product\src\entities\productQuote\ProductQuote;
use src\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\VarDumper;
use src\dto\email\EmailDTO;
use src\services\email\EmailMainService;
use src\exception\CreateModelException;
use src\exception\EmailNotSentException;

/**
 * @property string $template
 * @property EmailMainService $emailService
 */
class EmailConfirmationSender
{
    private string $template;

    private EmailMainService $emailService;

    public function __construct(string $template = 'order_update')
    {
        $this->template = $template;
        $this->emailService = EmailMainService::newInstance();
    }

    public function sendWithoutAttachments(Order $order): void
    {
        $this->send($order, []);
    }

    public function sendWithAllAttachments(Order $order): void
    {
        $files = [];
        $receipt = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_RECEIPT,
            'fo_or_id' => $order->or_id
        ])->one();
        if (!$receipt) {
            throw new \DomainException('Not found Receipt File. OrderId: ' . $order->or_id);
        }

        $files[] = new \modules\fileStorage\src\services\url\FileInfo(
            $receipt->file->fs_name,
            $receipt->file->fs_path,
            $receipt->file->fs_uid,
            $receipt->file->fs_title,
            null
        );

        $quotes = ProductQuote::find()->select(['pq_id'])->byOrderId($order->or_id)->booked()->column();
        foreach ($quotes as $quote) {
            if (!$fileProductQuotes = FileProductQuoteQuery::getByQuoteOrderAndCategory($quote, $order->or_id, FileOrder::CATEGORY_CONFIRMATION)) {
                throw new \DomainException('Not found File Confirmation. QuoteId: ' . $quote . ' OrderId: ' . $order->or_id);
            }
            foreach ($fileProductQuotes as $fileProductQuote) {
                $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                    $fileProductQuote->file->fs_name,
                    $fileProductQuote->file->fs_path,
                    $fileProductQuote->file->fs_uid,
                    $fileProductQuote->file->fs_title,
                    null
                );
            }
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

        if ($fileProductQuotes = FileProductQuoteQuery::getByOrderAndCategory($order->or_id, FileOrder::CATEGORY_CONFIRMATION)) {
            foreach ($fileProductQuotes as $fileProductQuote) {
                $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                    $fileProductQuote->file->fs_name,
                    $fileProductQuote->file->fs_path,
                    $fileProductQuote->file->fs_uid,
                    $fileProductQuote->file->fs_title,
                    null
                );
            }
        }

        $fileStorageUrlGenerator = \Yii::createObject(\modules\fileStorage\src\services\url\UrlGenerator::class);
        $attachments['files'] = $fileStorageUrlGenerator->generateForExternal($files);

        $this->send($order, $attachments);
    }

    private function send(Order $order, array $files): void
    {
        $projectId = $order->or_project_id ?? null;

        if (!$projectId) {
            \Yii::error([
                'message' => 'Not found Project',
                'orderId' => $order->or_id,
            ], 'OrderCanceledConfirmationJob');
            return;
        }

        $project = $order->project;
        $from = $project->getEmailNoReply();
        $fromName = $project->getEmailFromName() ?: $project->name;

        $orderContacts = OrderContact::find()
            ->select('oc_email')
            ->distinct()
            ->byOrderId($order->or_id)
            ->all();

        if (!$orderContacts) {
            throw new \DomainException('Order Contacts not found by order id: ' . $order->or_id);
        }

        $mailPreviewErrors = [];

        $languageId = $this->getLanguage($order);

        foreach ($orderContacts as $orderContact) {
            $mailPreview = \Yii::$app->comms->mailPreview(
                $projectId,
                $this->template,
                $from,
                $orderContact->oc_email,
                (new EmailConfirmationData())->generate($order),
            );

            if ($mailPreview['error'] !== false) {
                $mailPreviewErrors[] = $this->mailPreviewError($from, $orderContact->oc_email, $mailPreview['error']);
            } else {
                $this->sendEmail(
                    $order,
                    $this->template,
                    $from,
                    $fromName,
                    $orderContact->oc_email,
                    $languageId,
                    $mailPreview['data']['email_subject'],
                    $mailPreview['data']['email_body_html'],
                    $files
                );
            }
        }

        if ($mailPreviewErrors) {
            throw new \DomainException(implode('; ', $mailPreviewErrors));
        }
    }

    private function getLanguage(Order $order): string
    {
        $data = OrderData::find()->select(['od_language_id'])->byOrderId($order->or_id)->asArray()->one();
        if ($data && $data['od_language_id']) {
            return $data['od_language_id'];
        }
        $locale = ProjectLocale::find()->select(['pl_language_id'])->andWhere([
            'pl_project_id' => $order->or_project_id,
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
        try {
            $emailDTO = EmailDTO::newInstance()->fillFromOrderConfirm(
                $order,
                $templateKey,
                $from,
                $fromName,
                $to,
                $languageId,
                $subject,
                $body,
                $files
            );

            $mail = $this->emailService->createFromDTO($emailDTO, false);

            $orderEmail = new OrderEmail();
            $orderEmail->oe_order_id = $order->or_id;
            $orderEmail->oe_email_id = $mail->e_id;
            $orderEmail->save();

            $this->emailService->sendMail($mail, $files);
        } catch (CreateModelException $e) {
            throw new \DomainException(VarDumper::dumpAsString($e->getErrors()));
        } catch (EmailNotSentException $e) {
            throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.');
        } catch (\Throwable $e) {
            throw new \DomainException(VarDumper::dumpAsString($e->getMessage()));
        }
    }

    private function mailPreviewError(string $from, string $to, string $message): string
    {
        return 'Sending email from ' . $from . ' to ' . $to . ' failed: ' . $message;
    }
}
