<?php

namespace src\model\client\notifications\sms;

use common\models\ClientPhone;
use common\models\Sms;
use modules\product\src\entities\productQuote\ProductQuote;
use src\helpers\ErrorsToStringHelper;
use src\helpers\ProjectHashGenerator;
use src\model\client\notifications\client\entity\ClientNotificationRepository;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;
use src\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;
use src\model\client\notifications\sms\entity\Status;
use src\model\phoneList\entity\PhoneList;

/**
 * Class ClientNotificationSmsExecutor
 *
 * @property ClientNotificationSmsListRepository $clientNotificationSmsListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationSmsExecutor
{
    private ClientNotificationSmsListRepository $clientNotificationSmsListRepository;
    private ClientNotificationRepository $clientNotificationRepository;

    public function __construct(
        ClientNotificationRepository $clientNotificationRepository,
        ClientNotificationSmsListRepository $clientNotificationSmsListRepository
    ) {
        $this->clientNotificationSmsListRepository = $clientNotificationSmsListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function execute(ClientNotificationSmsList $notification): void
    {
        if (!$notification->isNew()) {
            throw new \DomainException('Notification status invalid. Wait: "new", current: "' . Status::getName($notification->cnsl_status_id) . '" . ID: ' . $notification->cnsl_id);
        }

        $fromPhone = PhoneList::find()->select(['pl_phone_number'])->andWhere(['pl_id' => $notification->cnsl_from_phone_id])->scalar();
        if (!$fromPhone) {
            throw new \DomainException('Not found Phone From. PhoneListId: ' . $notification->cnsl_from_phone_id . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

        $toPhone = ClientPhone::find()->select(['phone'])->andWhere(['id' => $notification->cnsl_to_client_phone_id])->scalar();
        if (!$toPhone) {
            throw new \DomainException('Not found Client Phone. ClientPhoneId: ' . $notification->cnsl_to_client_phone_id . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

        if (!$notification->getData()->templateKey) {
            throw new \DomainException('Template Key is empty. SmsNotificationId: ' . $notification->cnsl_id);
        }

        $quote = ProductQuote::find()->byId($notification->getData()->productQuoteId)->one();
        if (!$quote) {
            throw new \DomainException('Not found Product Quote. Product Quote Id: ' . $notification->getData()->productQuoteId . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

        $bookingId = $quote->getLastBookingId();
        if (!$bookingId) {
            throw new \DomainException('Not found BookingId. Product Quote Id: ' . $notification->getData()->productQuoteId . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

        $bookingHashCode = ProjectHashGenerator::getHashByProjectId($notification->getData()->projectId, $bookingId);

        // todo
        $languageId = 'en-US';

        try {
            $smsText = $this->getSmsContent(
                $notification->cnsl_id,
                $notification->getData()->templateKey,
                [
                    'project_key' => $notification->getData()->projectKey,
                    'from_phone' => $fromPhone,
                    'name_from' => $notification->cnsl_name_from,
                    'to_phone' => $toPhone,
                    'booking_id' => $bookingId,
                    'booking_hash_code' => $bookingHashCode,
                    'quote' => $quote->toArray(),
                ],
                $languageId
            );

            $sms = new Sms();
            $sms->s_project_id = $notification->getData()->projectId;
            $sms->s_phone_from = $fromPhone;
            $sms->s_phone_to = $toPhone;
            $sms->s_sms_text = $smsText;
            $sms->s_type_id = Sms::TYPE_OUTBOX;
            $sms->s_language_id = $languageId;
            $sms->s_is_new = true;
            $sms->s_status_id = Sms::STATUS_PENDING;
            $sms->s_created_dt = date('Y-m-d H:i:s');
            $sms->s_case_id = $notification->getData()->caseId;
            $sms->s_client_id = $notification->getData()->clientId;
            if (!$sms->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($sms));
            }

            $notification->processing($sms->s_id, new \DateTimeImmutable());
            $this->clientNotificationSmsListRepository->save($notification);

            $result = $sms->sendSms();

            if ($result['error']) {
                $notification->error(new \DateTimeImmutable());
                $this->clientNotificationSmsListRepository->save($notification);
                return;
            }

            $notification->done(new \DateTimeImmutable());
            $this->clientNotificationSmsListRepository->save($notification);
        } catch (\Throwable $e) {
            $notification->error(new \DateTimeImmutable());
            $this->clientNotificationSmsListRepository->save($notification);
            throw $e;
        }
    }

    private function getSmsContent(int $notificationId, string $templateKey, array $contentData, string $languageId): string
    {
        $result = \Yii::$app->comms->getContent(
            $templateKey,
            $contentData,
            $languageId,
        );

        if ($result['error'] !== false) {
            throw new \DomainException('Cant load SMS content. NotificationId: ' . $notificationId);
        }

        $content = $result['content'] ?? null;
        if ($content) {
            return $content;
        }

        throw new \DomainException('Received SMS content is empty. NotificationId: ' . $notificationId);
    }
}
