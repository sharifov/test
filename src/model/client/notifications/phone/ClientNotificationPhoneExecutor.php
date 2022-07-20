<?php

namespace src\model\client\notifications\phone;

use common\models\ClientPhone;
use src\model\client\notifications\client\entity\ClientNotificationRepository;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use src\model\client\notifications\phone\entity\Status;
use src\model\phoneList\entity\PhoneList;

/**
 * Class ClientNotificationPhoneExecutor
 *
 * @property ClientNotificationPhoneListRepository $clientNotificationPhoneListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationPhoneExecutor
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

    public function execute(ClientNotificationPhoneList $notification): void
    {
        if (!$notification->isNew()) {
            throw new \DomainException('Notification status invalid. Wait: "new", current: "' . Status::getName($notification->cnfl_status_id) . '" . ID: ' . $notification->cnfl_id);
        }

        $fromPhone = PhoneList::find()->select(['pl_phone_number'])->andWhere(['pl_id' => $notification->cnfl_from_phone_id])->scalar();
        if (!$fromPhone) {
            throw new \DomainException('Not found Phone From. PhoneListId: ' . $notification->cnfl_from_phone_id . ' PhoneNotificationId: ' . $notification->cnfl_id);
        }

        $toPhone = ClientPhone::find()->select(['phone'])->andWhere(['id' => $notification->cnfl_to_client_phone_id])->scalar();
        if (!$toPhone) {
            throw new \DomainException('Not found Client Phone. ClientPhoneId: ' . $notification->cnfl_to_client_phone_id . ' PhoneNotificationId: ' . $notification->cnfl_id);
        }

        // todo change when will work message template on communication
        if (!$notification->cnfl_message && !$notification->cnfl_file_url) {
            throw new \DomainException('Notification message and File url is empty. PhoneNotificationId: ' . $notification->cnfl_id);
        }

        try {
            $notification->processing(new \DateTimeImmutable());
            $this->clientNotificationPhoneListRepository->save($notification);

            $callSid = \Yii::$app->comms->makeCallClientNotification(
                $fromPhone,
                $toPhone,
                $notification->cnfl_message,
                $notification->getData()->sayVoice,
                $notification->getData()->sayLanguage,
                $notification->cnfl_file_url,
                [
                    'client_id' => $notification->getData()->clientId,
                    'project_id' => $notification->getData()->projectId,
                    'case_id' => $notification->getData()->caseId,
                    'phone_list_id' => $notification->cnfl_from_phone_id,
                ]
            );

            $notification->done($callSid, new \DateTimeImmutable());
            $this->clientNotificationPhoneListRepository->save($notification);
        } catch (\Throwable $e) {
            $notification->error(new \DateTimeImmutable());
            $this->clientNotificationPhoneListRepository->save($notification);
            throw $e;
        }
    }
}
