<?php

namespace src\model\client\notifications;

use src\model\client\notifications\client\entity\ClientNotification;
use src\model\client\notifications\client\entity\ClientNotificationRepository;
use src\model\client\notifications\client\entity\CommunicationType;
use src\model\client\notifications\client\entity\NotificationType;
use src\model\client\notifications\email\entity\ClientNotificationEmailList;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use src\model\client\notifications\phone\entity\Data as PhoneData;
use src\model\client\notifications\email\entity\Data as EmailData;
use src\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;
use src\model\client\notifications\email\entity\ClientNotificationEmailListRepository;
use src\model\client\notifications\sms\entity\Data as SmsData;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;

/**
 * Class ClientNotificationCreator
 *
 * @property ClientNotificationPhoneListRepository $notificationPhoneListRepository
 * @property ClientNotificationSmsListRepository $notificationSmsListRepository
 * @property ClientNotificationEmailListRepository $notificationEmailListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationCreator
{
    private ClientNotificationPhoneListRepository $notificationPhoneListRepository;
    private ClientNotificationRepository $clientNotificationRepository;
    private ClientNotificationSmsListRepository $notificationSmsListRepository;
    private ClientNotificationEmailListRepository $notificationEmailListRepository;

    public function __construct(
        ClientNotificationPhoneListRepository $notificationPhoneListRepository,
        ClientNotificationSmsListRepository $notificationSmsListRepository,
        ClientNotificationEmailListRepository $notificationEmailListRepository,
        ClientNotificationRepository $clientNotificationRepository
    ) {
        $this->notificationPhoneListRepository = $notificationPhoneListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->notificationSmsListRepository = $notificationSmsListRepository;
        $this->notificationEmailListRepository = $notificationEmailListRepository;
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

    public function createEmailNotification(
        int $fromEmailId,
        string $nameFrom,
        int $toClientEmailId,
        \DateTimeImmutable $startDt,
        \DateTimeImmutable $endDt,
        EmailData $data,
        \DateTimeImmutable $createdDt,
        int $clientId,
        NotificationType $type,
        int $objectId
    ): void {
        $emailList = ClientNotificationEmailList::create(
            $fromEmailId,
            $nameFrom,
            $toClientEmailId,
            $startDt,
            $endDt,
            $data,
            $createdDt
        );
        $this->notificationEmailListRepository->save($emailList);

        $clientNotification = ClientNotification::create(
            $clientId,
            $type,
            $objectId,
            CommunicationType::EMAIL,
            $emailList->cnel_id,
            $createdDt
        );
        $this->clientNotificationRepository->save($clientNotification);
    }
}
