<?php

namespace src\model\client\notifications;

use src\helpers\app\AppHelper;
use src\model\client\notifications\client\entity\ClientNotification;
use src\model\client\notifications\client\entity\CommunicationType;
use src\model\client\notifications\email\entity\ClientNotificationEmailListRepository;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use src\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;

/**
 * Class ClientNotificationCanceler
 *
 * @property ClientNotificationPhoneListRepository $phoneRepository
 * @property ClientNotificationSmsListRepository $smsRepository
 * @property ClientNotificationEmailListRepository $emailRepository
 */
class ClientNotificationCanceler
{
    private ClientNotificationPhoneListRepository $phoneRepository;
    private ClientNotificationSmsListRepository $smsRepository;
    private ClientNotificationEmailListRepository $emailRepository;

    public function __construct(
        ClientNotificationPhoneListRepository $phoneRepository,
        ClientNotificationSmsListRepository $smsRepository,
        ClientNotificationEmailListRepository $emailRepository
    ) {
        $this->phoneRepository = $phoneRepository;
        $this->smsRepository = $smsRepository;
        $this->emailRepository = $emailRepository;
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

            if ((int)$notification['type'] === CommunicationType::EMAIL) {
                $this->cancelEmailNotification($notification['id']);
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

    private function cancelEmailNotification(int $notificationId): void
    {
        try {
            $notification = $this->emailRepository->find($notificationId);
            if (!$notification->isNew()) {
                return;
            }
            $notification->cancel(new \DateTimeImmutable());
            $this->emailRepository->save($notification);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client email notification cancel error',
                'notificationId' => $notificationId,
                'exception' => AppHelper::throwableLog($e),
            ], 'ClientNotificationCanceler:cancelEmailNotification');
        }
    }
}
