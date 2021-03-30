<?php

namespace modules\order\src\processManager;

use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository as PhoneToBookRepository;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository as ClickToBookRepository;

/**
 * Class OrderProcessManagerCanceler
 *
 * @property PhoneToBookRepository $phoneToBookRepository
 * @property ClickToBookRepository $clickToBookRepository
 */
class OrderProcessManagerCanceler
{
    private PhoneToBookRepository $phoneToBookRepository;
    private ClickToBookRepository $clickToBookRepository;

    public function __construct(
        PhoneToBookRepository $phoneToBookRepository,
        ClickToBookRepository $clickToBookRepository
    ) {
        $this->phoneToBookRepository = $phoneToBookRepository;
        $this->clickToBookRepository = $clickToBookRepository;
    }

    public function cancel(int $orderId): void
    {
        $clickToBookManager = $this->clickToBookRepository->get($orderId);
        if ($clickToBookManager) {
            $clickToBookManager->cancel(new \DateTimeImmutable());
            $this->clickToBookRepository->save($clickToBookManager);
            return;
        }

        $phoneToBookManager = $this->phoneToBookRepository->get($orderId);
        if ($phoneToBookManager) {
            $phoneToBookManager->cancel(new \DateTimeImmutable());
            $this->phoneToBookRepository->save($phoneToBookManager);
            return;
        }

        throw new \DomainException('Order Process Manager not found.');
    }
}
