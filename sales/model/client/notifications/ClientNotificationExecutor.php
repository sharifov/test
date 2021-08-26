<?php

namespace sales\model\client\notifications;

use common\models\ClientPhone;
use sales\model\client\notifications\client\entity\ClientNotificationRepository;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use sales\model\client\notifications\phone\entity\Status;
use sales\model\phoneList\entity\PhoneList;

/**
 * Class ClientNotificationExecutor
 *
 * @property ClientNotificationPhoneListRepository $clientNotificationPhoneListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationExecutor
{
    private ClientNotificationPhoneListRepository $clientNotificationPhoneListRepository;
    private ClientNotificationRepository $clientNotificationRepository;

    public function __construct(
        ClientNotificationRepository $clientNotificationRepository,
        ClientNotificationPhoneListRepository $clientNotificationPhoneListRepository
    ) {
        $this->clientNotificationPhoneListRepository = $clientNotificationPhoneListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function execute(int $notificationId): void
    {
        $notification = $this->clientNotificationRepository->find($notificationId);

        if ($notification->isPhone()) {
            $this->cratedCall($notification->cn_communication_object_id);
            return;
        }
    }

    private function cratedCall(int $notificationId): void
    {
        $notification = $this->clientNotificationPhoneListRepository->find($notificationId);
        if (!$notification->isNew()) {
            throw new \DomainException('Notification status invalid. Wait: "new", current: "' . Status::getName($notification->cnfl_status_id) . '" . ID: ' . $notificationId);
        }

        $fromPhone = PhoneList::find()->select(['pl_phone_number'])->andWhere(['pl_id' => $notification->cnfl_from_phone_id])->scalar();
        if (!$fromPhone) {
            throw new \DomainException('Not found Phone From. PhoneListId: ' . $notification->cnfl_from_phone_id . ' PhoneNotificationId: ' . $notificationId);
        }

        $toPhone = ClientPhone::find()->select(['phone'])->andWhere(['id' => $notification->cnfl_to_client_phone_id])->scalar();
        if (!$toPhone) {
            throw new \DomainException('Not found Client Phone. ClientPhoneId: ' . $notification->cnfl_to_client_phone_id . ' PhoneNotificationId: ' . $notificationId);
        }

        try {
            $callSid = \Yii::$app->communication->makeCallClientNotification(
                $fromPhone,
                $toPhone,
                $notification->cnfl_message,
                $notification->getData()->sayVoice,
                $notification->getData()->sayLanguage,
                $notification->cnfl_file_url,
                [
                    'project_id' => $notification->getData()->projectId,
                    'client_id' => $notification->clientNotification->cn_client_id,
                    'case_id' => $notification->getData()->caseId,
                    'phone_list_id' => $notification->cnfl_from_phone_id,
                ]
            );
            $notification->processing($callSid, new \DateTimeImmutable());
            $this->clientNotificationPhoneListRepository->save($notification);
        } catch (\Throwable $e) {
            $notification->error(new \DateTimeImmutable());
            $this->clientNotificationPhoneListRepository->save($notification);
            throw $e;
        }
    }

    private function sendSms(): void
    {
    }

    private function sendEmail(): void
    {
    }
}
