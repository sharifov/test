<?php

namespace src\model\client\notifications\email;

use common\models\ClientEmail;
use common\models\Email;
use common\models\EmailTemplateType;
use modules\product\src\entities\productQuote\ProductQuote;
use src\entities\cases\Cases;
use src\helpers\ErrorsToStringHelper;
use src\helpers\ProjectHashGenerator;
use src\model\client\notifications\client\entity\ClientNotificationRepository;
use src\model\client\notifications\email\entity\ClientNotificationEmailList;
use src\model\client\notifications\email\entity\ClientNotificationEmailListRepository;
use src\model\client\notifications\email\entity\Status;
use src\model\emailList\entity\EmailList;
use yii\helpers\ArrayHelper;
use src\services\cases\CasesCommunicationService;

/**
 * Class ClientNotificationEmailExecutor
 *
 * @property ClientNotificationEmailListRepository $clientNotificationEmailListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationEmailExecutor
{
    private ClientNotificationEmailListRepository $clientNotificationEmailListRepository;
    private ClientNotificationRepository $clientNotificationRepository;
    private CasesCommunicationService $casesCommunicationService;

    public function __construct(
        ClientNotificationRepository $clientNotificationRepository,
        ClientNotificationEmailListRepository $clientNotificationEmailListRepository,
        CasesCommunicationService $casesCommunicationService
    ) {
        $this->clientNotificationEmailListRepository = $clientNotificationEmailListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->casesCommunicationService = $casesCommunicationService;
    }

    public function execute(ClientNotificationEmailList $notification): void
    {
        if (!$notification->isNew()) {
            throw new \DomainException('Notification status invalid. Wait: "new", current: "' . Status::getName($notification->cnel_status_id) . '" . ID: ' . $notification->cnel_id);
        }

        $fromEmail = EmailList::find()->select(['el_email'])->andWhere(['el_id' => $notification->cnel_from_email_id])->scalar();
        if (!$fromEmail) {
            throw new \DomainException('Not found Email From. EmailListId: ' . $notification->cnel_from_email_id . ' EmailNotificationId: ' . $notification->cnel_id);
        }

        $toEmail = ClientEmail::find()->select(['email'])->andWhere(['id' => $notification->cnel_to_client_email_id])->scalar();
        if (!$toEmail) {
            throw new \DomainException('Not found Client Email. ClientEmailId: ' . $notification->cnel_to_client_email_id . ' EmailNotificationId: ' . $notification->cnel_id);
        }

        if (!$notification->getData()->templateKey) {
            throw new \DomainException('Template Key is empty. EmailNotificationId: ' . $notification->cnel_id);
        }

        $quote = ProductQuote::find()->byId($notification->getData()->productQuoteId)->one();
        if (!$quote) {
            throw new \DomainException('Not found Product Quote. Product Quote Id: ' . $notification->getData()->productQuoteId . ' EmailNotificationId: ' . $notification->cnel_id);
        }

        $bookingId = $quote->getLastBookingId();
        if (!$bookingId) {
            throw new \DomainException('Not found BookingId. Product Quote Id: ' . $notification->getData()->productQuoteId . ' EmailNotificationId: ' . $notification->cnel_id);
        }

        $bookingHashCode = ProjectHashGenerator::getHashByProjectId($notification->getData()->projectId, $bookingId);
        $languageId = 'en-US';

        try {
            $case = Cases::findOne($notification->getData()->caseId);
            $emailData = $this->casesCommunicationService->getEmailDataWithoutAgentData($case);
            $emailData['reprotection_quote'] = $quote->serialize();
            $emailData['original_quote'] = $quote->serialize();
            $emailData['booking_hash_code'] = $bookingHashCode;
            if (!empty($emailData['reprotection_quote']['data'])) {
                ArrayHelper::remove($emailData['reprotection_quote']['data'], 'fq_origin_search_data');
            }
            if (!empty($emailData['original_quote']['data'])) {
                ArrayHelper::remove($emailData['original_quote']['data'], 'fq_origin_search_data');
            }

            $emailTemplateTypeId = EmailTemplateType::find()
                ->select(['etp_id'])
                ->andWhere(['etp_key' => $notification->getData()->templateKey])
                ->scalar();

            if (!$emailTemplateTypeId) {
                throw new \DomainException('Not found Email Template Type. Email Template Type Key: ' . $notification->getData()->templateKey . ' EmailNotificationId: ' . $notification->cnel_id);
            }

            $previewEmail = $this->getEmailPreview(
                $notification->cnel_id,
                $notification->getData()->projectId,
                $notification->getData()->templateKey,
                $fromEmail,
                $toEmail,
                $emailData,
                $languageId
            );

            $mail = new Email();
            $mail->e_project_id = $notification->getData()->projectId;
            $mail->e_case_id = $notification->getData()->caseId;
            $mail->e_template_type_id = $emailTemplateTypeId;
            $mail->e_type_id = Email::TYPE_OUTBOX;
            $mail->e_status_id = Email::STATUS_PENDING;
            $mail->e_email_subject = $previewEmail['email_subject'] ?? null;
            $mail->body_html = $previewEmail['email_body_html'] ?? null;
            $mail->e_email_from = $fromEmail;
            $mail->e_email_from_name = $notification->cnel_name_from;
            $mail->e_language_id = $languageId;
            $mail->e_email_to = $toEmail;
            //$mail->email_data = [];
            $mail->e_created_dt = date('Y-m-d H:i:s');


            if (!$mail->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($mail));
            }

            $mail->e_message_id = $mail->generateMessageId();
            $mail->update();

            $notification->processing($mail->e_id, new \DateTimeImmutable());
            $this->clientNotificationEmailListRepository->save($notification);

            $result = $mail->sendMail();

            if ($result['error']) {
                $notification->error(new \DateTimeImmutable());
                $this->clientNotificationEmailListRepository->save($notification);
                return;
            }

            $notification->done(new \DateTimeImmutable());
            $this->clientNotificationEmailListRepository->save($notification);
        } catch (\Throwable $e) {
            $notification->error(new \DateTimeImmutable());
            $this->clientNotificationEmailListRepository->save($notification);
            throw $e;
        }
    }

    private function getEmailPreview(int $notificationId, int $projectId, string $templateKey, string $fromEmail, string $toEmail, array $contentData, string $languageId): array
    {
        $result = \Yii::$app->comms->mailPreview(
            $projectId,
            $templateKey,
            $fromEmail,
            $toEmail,
            $contentData,
            $languageId,
        );

        if ($result['error'] !== false) {
            throw new \DomainException('Cant load Email content. NotificationId: ' . $notificationId);
        }

        $data = $result['data'] ?? null;
        if ($data) {
            return $data;
        }

        throw new \DomainException('Received Email content is empty. NotificationId: ' . $notificationId);
    }
}
