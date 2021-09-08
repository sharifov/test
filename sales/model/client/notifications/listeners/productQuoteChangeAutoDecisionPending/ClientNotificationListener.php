<?php

namespace sales\model\client\notifications\listeners\productQuoteChangeAutoDecisionPending;

use common\models\Airports;
use common\models\ClientPhone;
use modules\flight\models\FlightQuote;
use modules\order\src\entities\orderContact\OrderContact;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeAutoDecisionPendingEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\helpers\DayTimeHours;
use sales\helpers\setting\SettingHelper;
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
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 */
class ClientNotificationListener
{
    private ClientNotificationProjectSettings $projectSettings;
    private ClientNotificationCreator $clientNotificationCreator;
    private TransactionManager $transactionManager;
    private ClientManageService $clientManageService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    public function __construct(
        ClientNotificationProjectSettings $projectSettings,
        ClientNotificationCreator $clientNotificationCreator,
        TransactionManager $transactionManager,
        ClientManageService $clientManageService,
        ProductQuoteChangeRepository $productQuoteChangeRepository
    ) {
        $this->projectSettings = $projectSettings;
        $this->clientNotificationCreator = $clientNotificationCreator;
        $this->transactionManager = $transactionManager;
        $this->clientManageService = $clientManageService;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function handle(ProductQuoteChangeAutoDecisionPendingEvent $event): void
    {
        try {
            $productQuoteChange = $this->productQuoteChangeRepository->find($event->productQuoteChangeId);

            $project = new Project(
                $productQuoteChange->pqcPq->pqProduct->project->id,
                $productQuoteChange->pqcPq->pqProduct->project->project_key
            );
            $notificationType = NotificationType::fromEvent($event);

            $notificationSettings = $this->projectSettings->getNotificationSettings($project->id, $notificationType->getType());
            if (!$notificationSettings) {
                return;
            }
            if (!$notificationSettings->isAnyEnabled()) {
                return;
            }

            $client = $this->getClient($productQuoteChange);

            if ($client->phoneId && $notificationSettings->sendPhoneNotification->enabled) {
                $this->phoneNotificationProcessing($project, $notificationType, $client, $notificationSettings->sendPhoneNotification, $productQuoteChange);
            }

            if ($client->phoneId && $notificationSettings->sendSmsNotification->enabled) {
                $this->smsNotificationProcessing($project, $notificationType, $client, $notificationSettings->sendSmsNotification, $productQuoteChange);
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'event' => [
                    'name' => ProductQuoteChangeAutoDecisionPendingEvent::class,
                    'productQuoteChangeId' => $event->productQuoteChangeId,
                ],
                'exception' => AppHelper::throwableLog($e, true),
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:handle');
        }
    }

    private function phoneNotificationProcessing(
        Project $project,
        NotificationType $notificationType,
        Client $client,
        SendPhoneNotification $settings,
        ProductQuoteChange $productQuoteChange
    ): void {
        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:phoneNotificationProcessing');
            return;
        }

        $time = $this->getTime($productQuoteChange->pqc_pq_id);

        $this->transactionManager->wrap(function () use ($phoneFromId, $client, $time, $settings, $notificationType, $project, $productQuoteChange) {
            $this->clientNotificationCreator->createPhoneNotification(
                $phoneFromId,
                $client->phoneId,
                $time->start,
                $time->end,
                $time->fromHours,
                $time->toHours,
                $settings->messageSay,
                $settings->fileUrl,
                PhoneData::createFromArray([
                    'clientId' => $client->id,
                    'caseId' => $productQuoteChange->pqc_case_id,
                    'projectId' => $project->id,
                    'projectKey' => $project->key,
                    'sayVoice' => $settings->messageSayVoice,
                    'sayLanguage' => $settings->messageSayLanguage,
                ]),
                new \DateTimeImmutable(),
                $client->id,
                $notificationType,
                $productQuoteChange->pqc_id
            );
        });
    }

    private function smsNotificationProcessing(
        Project $project,
        NotificationType $notificationType,
        Client $client,
        SendSmsNotification $settings,
        ProductQuoteChange $productQuoteChange
    ): void {
        if (!$settings->messageTemplateKey) {
            \Yii::error([
                'message' => 'Sms template Key is empty',
                'projectId' => $project->id,
                'notificationType' => $notificationType->getType(),
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
                'productQuoteId' => $productQuoteChange->pqc_pq_id,
                'caseId' => $productQuoteChange->pqc_case_id,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:smsNotificationProcessing');
            return;
        }
        $templateKey = $settings->messageTemplateKey;

        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
                'productQuoteId' => $productQuoteChange->pqc_pq_id,
                'caseId' => $productQuoteChange->pqc_case_id,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:smsNotificationProcessing');
            return;
        }

        $time = $this->getTime($productQuoteChange->pqc_pq_id);

        $this->transactionManager->wrap(function () use (
            $phoneFromId,
            $client,
            $time,
            $settings,
            $notificationType,
            $project,
            $templateKey,
            $productQuoteChange
        ) {
            $this->clientNotificationCreator->createSmsNotification(
                $phoneFromId,
                $settings->nameFrom,
                $client->phoneId,
                $time->start,
                $time->end,
                SmsData::createFromArray([
                    'clientId' => $client->id,
                    'caseId' => $productQuoteChange->pqc_case_id,
                    'projectId' => $project->id,
                    'projectKey' => $project->key,
                    'templateKey' => $templateKey,
                    'productQuoteId' => (int)$productQuoteChange->pqc_pq_id,
                ]),
                new \DateTimeImmutable(),
                $client->id,
                $notificationType,
                $productQuoteChange->pqc_id
            );
        });
    }

    private function getClient(ProductQuoteChange $productQuoteChange): Client
    {
        $orderId = $productQuoteChange->pqcPq->pq_order_id ?? null;
        if (!$orderId) {
            throw new \DomainException('Not found Order.');
        }

        $contact = OrderContact::find()->select(['oc_id', 'oc_client_id', 'oc_phone_number'])->andWhere(['oc_order_id' => $orderId])->orderBy(['oc_id' => SORT_DESC])->asArray()->one();
        if (!$contact) {
            throw new \DomainException('Not found Contact.');
        }

        if (!$contact['oc_client_id']) {
            throw new \DomainException('Not found Client Id. Order contact Id: ' . $contact['oc_id']);
        }

        if (!$contact['oc_phone_number']) {
            throw new \DomainException('Not found Contact phone number.');
        }

        $clientPhoneId = ClientPhone::find()->select(['id'])->andWhere(['client_id' => $contact['oc_client_id'], 'phone' => $contact['oc_phone_number']])->scalar();
        if (!$clientPhoneId) {
            \Yii::error([
                'message' => 'Not found Contact Client Phone',
                'productQuoteChangeId' => $productQuoteChange->pqc_id,
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:getClient');
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
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:addClientPhone');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client phone not created',
                'clientId' => $clientId,
                'exception' => AppHelper::throwableLog($e, true),
            ], 'ProductQuoteChangeAutoDecisionPendingClientNotificationListener:addClientPhone');
        }
        return null;
    }

    private function getTime(int $productQuoteId): Time
    {
        $productQuoteRelation = ProductQuoteRelation::find()->byParentQuoteId($productQuoteId)->reprotection()->with(['pqrRelatedPq'])->one();
        if (!$productQuoteRelation) {
            throw new \DomainException('Not found Related Reprotection Quote. ProductQuoteId: ' . $productQuoteId);
        }
        $reprotectionQuote = $productQuoteRelation->pqrRelatedPq;
        // todo make method getTime for any product
        if (!$reprotectionQuote->isFlight()) {
            throw new \DomainException('Reprotection Quote is not Flight Quote. ProductQuoteId: ' . $productQuoteId . ' ProductQuoteRelationId: ' . $productQuoteRelation->pqr_related_pq_id);
        }

        /** @var FlightQuote $flightQuote */
        $flightQuote = $reprotectionQuote->childQuote;
        $firstDepartureSegment = $flightQuote->flightQuoteTrips[0]->flightQuoteSegments[0] ?? null;
        if (!$firstDepartureSegment) {
            throw new \DomainException('Not found first flight segment. ProductQuoteId: ' . $productQuoteId . ' ProductQuoteRelationId: ' . $productQuoteRelation->pqr_related_pq_id);
        }
        $departureAirportTimeZone = Airports::find()->select(['timezone'])->andWhere(['iata' => $firstDepartureSegment->fqs_departure_airport_iata])->scalar();
        if (!$departureAirportTimeZone) {
            throw new \DomainException('Not found Airport time zone. IATA: ' . $firstDepartureSegment->fqs_departure_airport_iata . '  ProductQuoteId: ' . $productQuoteId . ' ProductQuoteRelationId: ' . $productQuoteRelation->pqr_related_pq_id);
        }

        $startInterval = SettingHelper::getClientNotificationStartInterval();
        $startDate = (new \DateTimeImmutable())
            ->modify('+ ' . $startInterval['days'] . ' days + ' . $startInterval['hours'] . ' hours');
        $endDate = new \DateTimeImmutable($firstDepartureSegment->fqs_departure_dt);

        if ($startDate >= $endDate) {
            throw new \DomainException('"End"(' . $endDate->format('Y-m-d H:i:s') . ') time must less then "start"(' . $startDate->format('Y-m-d H:i:s') . ') time.');
        }

        $callTimeHoursSettings = new DayTimeHours(\Yii::$app->params['settings']['qcall_day_time_hours']);

        $targetDateTime = (new \DateTimeImmutable())->setTimezone(new \DateTimeZone($departureAirportTimeZone));
        $fromHours = $targetDateTime->setTime($callTimeHoursSettings->startHour, 0)->setTimezone(new \DateTimeZone('UTC'));
        $toHours = $targetDateTime->setTime($callTimeHoursSettings->endHour, 0)->setTimezone(new \DateTimeZone('UTC'));

        return new Time($startDate, $endDate, (int)$fromHours->format('H'), (int)$toHours->format('H'));
    }
}