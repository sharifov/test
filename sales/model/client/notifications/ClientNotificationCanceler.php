<?php

namespace sales\model\client\notifications;

use sales\helpers\app\AppHelper;
use sales\model\client\notifications\client\entity\ClientNotification;
use sales\model\client\notifications\client\entity\CommunicationType;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;

/**
 * Class ClientNotificationCanceler
 *
 * @property ClientNotificationPhoneListRepository $phoneRepository
 * @property ClientNotificationSmsListRepository $smsRepository
 */
class ClientNotificationCanceler
{
    private ClientNotificationPhoneListRepository $phoneRepository;
    private ClientNotificationSmsListRepository $smsRepository;

    public function __construct(ClientNotificationPhoneListRepository $phoneRepository, ClientNotificationSmsListRepository $smsRepository)
    {
        $this->phoneRepository = $phoneRepository;
        $this->smsRepository = $smsRepository;
    }

    public function cancel(int $typeId, int $objectId): void
    {
        $notifications = ClientNotification::find()
            ->select(['cn_communication_object_id as id', 'cn_communication_type_id as type'])
            ->andWhere([
                'cn_notification_type_id' => $typeId,
                'cn_object_id' => $objectId,
            ])
            ->asArray()
            ->all();

        if (!$notifications) {
            return;
        }

        foreach ($notifications as $notification) {
            if ((int)$notification['type'] === CommunicationType::PHONE) {
                $this->cancelPhoneNotification($notification['id']);
                continue;
            }

            if ((int)$notification['type'] === CommunicationType::SMS) {
                $this->cancelSmsNotification($notification['id']);
            }
        }
    }

    private function cancelPhoneNotification(int $notificationId): void
    {
        try {
            $notification = $this->phoneRepository->find($notificationId);
            if (!$notification->isNew()) {
                return;
            }
            $notification->cancel(new \DateTimeImmutable());
            $this->phoneRepository->save($notification);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client phone notification cancel error',
                'notificationId' => $notificationId,
                'exception' => AppHelper::throwableLog($e),
            ], 'ClientNotificationCanceler:cancelPhoneNotification');
        }
    }

    private function cancelSmsNotification(int $notificationId): void
    {
        try {
            $notification = $this->smsRepository->find($notificationId);
            if (!$notification->isNew()) {
                return;
            }
            $notification->cancel(new \DateTimeImmutable());
            $this->smsRepository->save($notification);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client sms notification cancel error',
                'notificationId' => $notificationId,
                'exception' => AppHelper::throwableLog($e),
            ], 'ClientNotificationCanceler:cancelSmsNotification');
        }
    }
}
