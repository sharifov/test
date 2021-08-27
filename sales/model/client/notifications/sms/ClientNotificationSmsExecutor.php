<?php

namespace sales\model\client\notifications\sms;

use common\models\ClientPhone;
use common\models\Sms;
use sales\helpers\ErrorsToStringHelper;
use sales\model\client\notifications\client\entity\ClientNotificationRepository;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsList;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;
use sales\model\client\notifications\sms\entity\Status;
use sales\model\phoneList\entity\PhoneList;

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

        try {
            if ($notification->getData()->templateKey) {
                $smsText = $this->getSmsText(
                    $notification->cnsl_id,
                    $notification->getData()->projectId,
                    $notification->getData()->templateKey,
                    $fromPhone,
                    $toPhone,
                    [
                        'content' => $notification->cnsl_message,
                        // todo other params
                    ],
                    'en-US' // todo
                );
            } else {
                $smsText = $notification->cnsl_message;
            }

            $sms = new Sms();
            $sms->s_project_id = $notification->getData()->projectId;
            $sms->s_phone_from = $fromPhone;
            $sms->s_phone_to = $toPhone;
            $sms->s_sms_text = $smsText;
            $sms->s_type_id = Sms::TYPE_OUTBOX;
            $sms->s_template_type_id = $notification->getData()->templateId;
            $sms->s_language_id = null; // todo
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

    private function getSmsText($notificationId, $projectId, $templateKey, $from, $to, $contentData, $languageId): string
    {
        $smsPreview = \Yii::$app->communication->smsPreview(
            $projectId,
            $templateKey,
            $from,
            $to,
            $contentData,
            $languageId
        );

        if ($smsPreview['error'] !== false) {
            throw new \DomainException('Cant load preview SMS. NotificationId: ' . $notificationId);
        }

        $text = $smsPreview['data']['sms_text'] ?? null;
        if ($text) {
            return $text;
        }

        throw new \DomainException('Received SMS text is empty. NotificationId: ' . $notificationId);
    }
}
