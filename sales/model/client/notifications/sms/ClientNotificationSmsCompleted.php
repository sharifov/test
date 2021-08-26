<?php

namespace sales\model\client\notifications\sms;

use sales\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;

/**
 * Class ClientNotificationSmsCompleted
 *
 * @property ClientNotificationSmsListRepository $repository
 */
class ClientNotificationSmsCompleted
{
    private ClientNotificationSmsListRepository $repository;

    public function __construct(ClientNotificationSmsListRepository $repository)
    {
        $this->repository = $repository;
    }

    public function complete(string $smsSid): void
    {
        $notification = $this->repository->findBySid($smsSid);
        $notification->done(new \DateTimeImmutable());
        $this->repository->save($notification);
    }
}
