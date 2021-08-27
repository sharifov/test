<?php

namespace sales\model\client\notifications\listeners\productQuoteChange;

use common\models\ClientPhone;
use common\models\SmsTemplateType;
use modules\order\src\entities\orderContact\OrderContact;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeCreatedEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\model\client\notifications\client\entity\NotificationType;
use sales\model\client\notifications\ClientNotificationCreator;
use sales\model\client\notifications\phone\entity\Data as PhoneData;
use sales\model\client\notifications\sms\entity\Data as SmsData;
use sales\model\client\notifications\settings\ClientNotificationProjectSettings;
use sales\model\phoneList\entity\PhoneList;
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
            $projectId = $event->productQuoteChange->pqcPq->pqProduct->pr_project_id;

            $notificationType = NotificationType::productQuoteChange();

            if (!$this->projectSettings->isAnyTypeNotificationEnabled($projectId, $notificationType->getType())) {
                return;
            }

            $client = $this->getClient($event->productQuoteChange);

            if ($client->phoneId && $this->projectSettings->isSendPhoneNotificationEnabled($projectId, $notificationType->getType())) {
                $this->phoneNotificationProcessing($event, $projectId, $notificationType, $client);
            }

            if ($client->phoneId && $this->projectSettings->isSendSmsNotificationEnabled($projectId, $notificationType->getType())) {
                $this->smsNotificationProcessing($event, $projectId, $notificationType, $client);
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
            ], 'ProductQuoteChangeClientNotificationListener');
        }
    }

    private function phoneNotificationProcessing(ProductQuoteChangeCreatedEvent $event, int $projectId, NotificationType $notificationType, Client $client): void
    {
        $settings = $this->projectSettings->getPhoneNotificationSettings($projectId, $notificationType->getType());

        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeClientNotificationListener:phoneNotificationProcessing');
            return;
        }

        $time = $this->getTime();

        $this->transactionManager->wrap(function () use ($phoneFromId, $client, $time, $settings, $event, $notificationType, $projectId) {
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
                    'projectId' => $projectId,
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

    private function smsNotificationProcessing(ProductQuoteChangeCreatedEvent $event, int $projectId, NotificationType $notificationType, Client $client): void
    {
        $settings = $this->projectSettings->getSmsNotificationSettings($projectId, $notificationType->getType());

        if (!$settings->message) {
            \Yii::error([
                'message' => 'Sms client notification Message setting is empty. Sms notification not created.',
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeClientNotificationListener:smsNotificationProcessing');
            return;
        }

        $phoneFromId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $settings->phoneFrom])->scalar();
        if (!$phoneFromId) {
            \Yii::error([
                'message' => 'Not found Phone List',
                'productQuoteChangeId' => $event->getId(),
                'productQuoteId' => $event->productQuoteId,
                'caseId' => $event->caseId,
                'phone' => $settings->phoneFrom,
            ], 'ProductQuoteChangeClientNotificationListener:smsNotificationProcessing');
            return;
        }

        $templateId = null;
        $templateKey = null;
        if ($settings->messageTemplateKey) {
            $templateId = SmsTemplateType::find()->select(['stp_id'])->andWhere(['stp_key' => $settings->messageTemplateKey])->scalar();
            if (!$templateId) {
                \Yii::error([
                    'message' => 'Not found Sms template. Key: ' . $settings->messageTemplateKey,
                    'productQuoteChangeId' => $event->getId(),
                    'productQuoteId' => $event->productQuoteId,
                    'caseId' => $event->caseId,
                    'phone' => $settings->phoneFrom,
                ], 'ProductQuoteChangeClientNotificationListener:smsNotificationProcessing');
                return;
            }
            $templateKey = $settings->messageTemplateKey;
        }

        $time = $this->getTime();

        $this->transactionManager->wrap(function () use ($phoneFromId, $client, $time, $settings, $event, $notificationType, $projectId, $templateId, $templateKey) {
            $this->clientNotificationCreator->createSmsNotification(
                $phoneFromId,
                $settings->nameFrom,
                $client->phoneId,
                $time->start,
                $time->end,
                $settings->message,
                SmsData::createFromArray([
                    'clientId' => $client->id,
                    'caseId' => $event->caseId,
                    'projectId' => $projectId,
                    'templateId' => $templateId,
                    'templateKey' => $templateKey,
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
            ], 'ProductQuoteChangeClientNotificationListener');
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
            ], 'ProductQuoteChangeClientNotificationListener');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client phone not created',
                'clientId' => $clientId,
                'exception' => AppHelper::throwableLog($e, true),
            ], 'ProductQuoteChangeClientNotificationListener');
        }
        return null;
    }

    private function getTime(): Time
    {
        // todo
        return new Time(null, null);
    }
}
