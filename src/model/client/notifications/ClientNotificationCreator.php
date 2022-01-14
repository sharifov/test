<?php

namespace src\model\client\notifications;

use src\model\client\notifications\client\entity\ClientNotification;
use src\model\client\notifications\client\entity\ClientNotificationRepository;
use src\model\client\notifications\client\entity\CommunicationType;
use src\model\client\notifications\client\entity\NotificationType;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use src\model\client\notifications\phone\entity\Data as PhoneData;
use src\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;
use src\model\client\notifications\sms\entity\Data as SmsData;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;

/**
 * Class ClientNotificationCreator
 *
 * @property ClientNotificationPhoneListRepository $notificationPhoneListRepository
 * @property ClientNotificationSmsListRepository $notificationSmsListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationCreator
{
    private ClientNotificationPhoneListRepository $notificationPhoneListRepository;
    private ClientNotificationRepository $clientNotificationRepository;
    private ClientNotificationSmsListRepository $notificationSmsListRepository;

    public function __construct(
        ClientNotificationPhoneListRepository $notificationPhoneListRepository,
        ClientNotificationSmsListRepository $notificationSmsListRepository,
        ClientNotificationRepository $clientNotificationRepository
    ) {
        $this->notificationPhoneListRepository = $notificationPhoneListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->notificationSmsListRepository = $notificationSmsListRepository;
    }

    public function createPhoneNotification(
        int $fromPhoneId,
        int $toClientPhoneId,
        \DateTimeImmutable $startDt,
        \DateTimeImmutable $endDt,
        int $fromHours,
        int $toHours,
        ?string $message,
        ?string $fileUrl,
        PhoneData $data,
        \DateTimeImmutable $createdDt,
        int $clientId,
        NotificationType $type,
        int $objectId
    ): void {
        $phoneList = ClientNotificationPhoneList::create(
            $fromPhoneId,
            $toClientPhoneId,
            $startDt,
            $endDt,
            $fromHours,
            $toHours,
            $message,
            $fileUrl,
            $data,
            $createdDt
        );
        $this->notificationPhoneListRepository->save($phoneList);

        $clientNotification = ClientNotification::create(
            $clientId,
            $type,
            $objectId,
            CommunicationType::PHONE,
            $phoneList->cnfl_id,
            $createdDt
        );
        $this->clientNotificationRepository->save($clientNotification);
    }

    public function createSmsNotification(
        int $fromPhoneId,
        string $nameFrom,
        int $toClientPhoneId,
        \DateTimeImmutable $startDt,
        \DateTimeImmutable $endDt,
        SmsData $data,
        \DateTimeImmutable $createdDt,
        int $clientId,
        NotificationType $type,
        int $objectId
    ): void {
        $smsList = ClientNotificationSmsList::create(
            $fromPhoneId,
            $nameFrom,
            $toClientPhoneId,
            $startDt,
            $endDt,
            $data,
            $createdDt
        );
        $this->notificationSmsListRepository->save($smsList);

        $clientNotification = ClientNotification::create(
            $clientId,
            $type,
            $objectId,
            CommunicationType::SMS,
            $smsList->cnsl_id,
            $createdDt
        );
        $this->clientNotificationRepository->save($clientNotification);
    }
}
