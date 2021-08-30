<?php

namespace sales\model\client\notifications\listeners\productQuoteChangeCreated;

use common\models\ClientPhone;
use common\models\SmsTemplateType;
use modules\flight\models\FlightQuoteFlight;
use modules\order\src\entities\orderContact\OrderContact;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeCreatedEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\helpers\ProjectHashGenerator;
use sales\model\client\notifications\client\entity\NotificationType;
use sales\model\client\notifications\ClientNotificationCreator;
use sales\model\client\notifications\phone\entity\Data as PhoneData;
use sales\model\client\notifications\sms\entity\Data as SmsData;
use sales\model\client\notifications\settings\ClientNotificationProjectSettings;
use sales\model\phoneList\entity\PhoneList;
use sales\model\project\entity\params\SendPhoneNotification;
use sales\model\project\entity\params\SendSmsNotification;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;

/**
 * Class ClientNotificationListener
 *
 * @property ClientNotificationProjectSettings $projectSettings
 * @property ClientNotificationCreator $clientNotificationCreator
 * @property TransactionManager $transactionManager
 * @property ClientManageService $clientManageService
 */
class ClientNotificationListener
{
    private ClientNotificationProjectSettings $projectSettings;
    private ClientNotificationCreator $clientNotificationCreator;
    private TransactionManager $transactionManager;
    private ClientManageService $clientManageService;

    public function __construct(
        ClientNotificationProjectSettings $projectSettings,
        ClientNotificationCreator $clientNotificationCreator,
        TransactionManager $transactionManager,
        ClientManageService $clientManageService
    ) {
        $this->projectSettings = $projectSettings;
        $this->clientNotificationCreator = $clientNotificationCreator;
        $this->transactionManager = $transactionManager;
        $this->clientManageService = $clientManageService;
    }

    public function handle(ProductQuoteChangeCreatedEvent $event): void
    {
        try {
            $project = new Project(
                $event->productQuoteChange->pqcPq->pqProduct->project->id,
                $event->productQuoteChange->pqcPq->pqProduct->project->project_key
            );
            $notificationType = NotificationType::fromEvent($event);

            $notificationSettings = $this->projectSettings->getNotificationSettings($project->id, $notificationType->getType());
            if (!$notificationSettings) {
                return;
            }
            if (!$notificationSettings->isAnyEnabled()) {
                return;
            }

            $client = $this->getClient($event->productQuoteChange);

            if ($client->phoneId && $notificationSettings->sendPhoneNotification->enabled) {
                $this->phoneNotificationProcessing($event, $project, $notificationType, $client, $notificationSettings->sendPhoneNotification);
            }

            if ($client->phoneId && $notificationSettings->sendSmsNotification->enabled) {
                $this->smsNotificationProcessing($event, $project, $notificationType, $client, $notificationSettings->sendSmsNotification);
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'event' => [
                    'name' => ProductQuoteChangeCreatedEvent::class,
                    'productQuoteChangeId' => $event->getId(),
                    'productQuoteId' => $event->productQuoteId,
                    'caseId' => $event->caseId,
                ],
                'exception' => AppHelper::throwableLog($e, true),
            ], 'ProductQuoteChangeCreatedClientNotificationListener:handle');
        }
    }

    private function phoneNotificationProcessing(ProductQuoteChangeCreatedEvent $event, Project $project, NotificationType $notificationType, Client $client, SendPhoneNotification $settings): void
    {
        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:phoneNotificationProcessing');
            return;
        }

        $time = $this->getTime();

        $this->transactionManager->wrap(function () use ($phoneFromId, $client, $time, $settings, $event, $notificationType, $project) {
            $this->clientNotificationCreator->createPhoneNotification(
                $phoneFromId,
                $client->phoneId,
                $time->start,
                $time->end,
                $settings->messageSay,
                $settings->fileUrl,
                PhoneData::createFromArray([
                    'clientId' => $client->id,
                    'caseId' => $event->caseId,
                    'projectId' => $project->id,
                    'projectKey' => $project->key,
                    'sayVoice' => $settings->messageSayVoice,
                    'sayLanguage' => $settings->messageSayLanguage,
                ]),
                new \DateTimeImmutable(),
                $client->id,
                $notificationType,
                $event->getId()
            );
        });
    }

    private function smsNotificationProcessing(ProductQuoteChangeCreatedEvent $event, Project $project, NotificationType $notificationType, Client $client, SendSmsNotification $settings): void
    {
        if (!$settings->messageTemplateKey) {
            \Yii::error([
                'message' => 'Sms template Key is empty',
                'projectId' => $project->id,
                'notificationType' => $notificationType->getType(),
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:smsNotificationProcessing');
            return;
        }
        $templateKey = $settings->messageTemplateKey;

        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:smsNotificationProcessing');
            return;
        }

        $bookingId = $this->getBookingId($event->productQuoteChange);
        if (!$bookingId) {
            \Yii::error([
                'message' => 'Not found bookingId',
                'productQuoteChangeId' => $event->productQuoteChange->pqc_id,
                'productQuoteId' => $event->productQuoteChange->pqc_pq_id,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:smsNotificationProcessing');
            return;
        }

        $bookingHashCode = ProjectHashGenerator::getHashByProjectId($project->id, $bookingId);

        $time = $this->getTime();

        $this->transactionManager->wrap(function () use (
            $phoneFromId,
            $client,
            $time,
            $settings,
            $event,
            $notificationType,
            $project,
            $templateKey,
            $bookingId,
            $bookingHashCode
        ) {
            $this->clientNotificationCreator->createSmsNotification(
                $phoneFromId,
                $settings->nameFrom,
                $client->phoneId,
                $time->start,
                $time->end,
                SmsData::createFromArray([
                    'clientId' => $client->id,
                    'caseId' => $event->caseId,
                    'projectId' => $project->id,
                    'projectKey' => $project->key,
                    'templateKey' => $templateKey,
                    'bookingId' => $bookingId,
                    'bookingHashCode' => $bookingHashCode,
                ]),
                new \DateTimeImmutable(),
                $client->id,
                $notificationType,
                $event->getId()
            );
        });
    }

    private function getClient(ProductQuoteChange $productQuoteChange): Client
    {
        $orderId = $productQuoteChange->pqcPq->pq_order_id ?? null;
        if (!$orderId) {
            throw new \DomainException('Not found Order.');
        }

        $contact = OrderContact::find()->select(['oc_client_id', 'oc_phone_number'])->andWhere(['oc_order_id' => $orderId])->orderBy(['oc_id' => SORT_DESC])->asArray()->one();
        if (!$contact) {
            throw new \DomainException('Not found Contact.');
        }

        if (!$contact['oc_phone_number']) {
            throw new \DomainException('Not found Contact phone number.');
        }

        $clientPhoneId = ClientPhone::find()->select(['id'])->andWhere(['client_id' => $contact['oc_client_id'], 'phone' => $contact['oc_phone_number']])->scalar();
        if (!$clientPhoneId) {
            \Yii::error([
                'message' => 'Not found Contact Client Phone',
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:getClient');
            $clientPhoneId = $this->addClientPhone($contact['oc_client_id'], $contact['oc_phone_number']);
        }

        // todo contact email processing

        return new Client((int)$contact['oc_client_id'], (int)$clientPhoneId, null);
    }

    private function addClientPhone(int $clientId, string $phone): ?int
    {
        try {
            $client = \common\models\Client::find()->byId($clientId)->one();
            $clientPhone = $this->clientManageService->addPhone($client, new PhoneCreateForm([
                'client_id' => $clientId,
                'phone' => $phone,
                'type' => ClientPhone::PHONE_NOT_SET,
                'comments' => 'Created on product quote change client notification action',
            ]));
            if ($clientPhone) {
                return $clientPhone->id;
            }
            \Yii::error([
                'message' => 'Client phone not created. Undefined reason.',
                'clientId' => $clientId,
            ], 'ProductQuoteChangeCreatedClientNotificationListener:addClientPhone');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client phone not created',
                'clientId' => $clientId,
                'exception' => AppHelper::throwableLog($e, true),
            ], 'ProductQuoteChangeCreatedClientNotificationListener:addClientPhone');
        }
        return null;
    }

    private function getTime(): Time
    {
        // todo
        return new Time(null, null);
    }

    private function getBookingId(ProductQuoteChange $change): ?string
    {
        if ($change->pqcPq->isFlight()) {
            $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $change->pqcPq->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
            if ($lastFlightQuoteFlightBookingId) {
                return $lastFlightQuoteFlightBookingId;
            }
        }
        return null;
    }
}
