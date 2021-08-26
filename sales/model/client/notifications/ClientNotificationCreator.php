<?php

namespace sales\model\client\notifications;

use sales\model\client\notifications\client\entity\ClientNotification;
use sales\model\client\notifications\client\entity\ClientNotificationRepository;
use sales\model\client\notifications\client\entity\CommunicationType;
use sales\model\client\notifications\client\entity\NotificationType;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;
use sales\model\client\notifications\phone\entity\Data;

/**
 * Class ClientNotificationCreator
 *
 * @property ClientNotificationPhoneListRepository $notificationPhoneListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationCreator
{
    private ClientNotificationPhoneListRepository $notificationPhoneListRepository;
    private ClientNotificationRepository $clientNotificationRepository;

    public function __construct(
        ClientNotificationPhoneListRepository $notificationPhoneListRepository,
        ClientNotificationRepository $clientNotificationRepository
    ) {
        $this->notificationPhoneListRepository = $notificationPhoneListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function createPhoneNotification(
        int $fromPhoneId,
        int $toClientPhoneId,
        ?\DateTimeImmutable $startDt,
        ?\DateTimeImmutable $endDt,
        ?string $message,
        ?string $fileUrl,
        Data $data,
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
}
