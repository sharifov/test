<?php

namespace sales\model\client\notifications\phone;

use sales\model\client\notifications\phone\entity\ClientNotificationPhoneListRepository;

/**
 * Class ClientNotificationPhoneCompleted
 *
 * @property ClientNotificationPhoneListRepository $repository
 */
class ClientNotificationPhoneCompleted
{
    private ClientNotificationPhoneListRepository $repository;

    public function __construct(ClientNotificationPhoneListRepository $repository)
    {
        $this->repository = $repository;
    }

    public function complete(string $callSid): void
    {
        $notification = $this->repository->findBySid($callSid);
        $notification->done(new \DateTimeImmutable());
        $this->repository->save($notification);
    }
}
