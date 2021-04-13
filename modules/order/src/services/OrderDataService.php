<?php

namespace modules\order\src\services;

use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataLanguage;
use modules\order\src\entities\orderData\OrderDataMarketCountry;
use modules\order\src\entities\orderData\OrderDataRepository;

/**
 * Class OrderDataService
 *
 * @property OrderDataRepository $repository
 */
class OrderDataService
{
    private OrderDataRepository $repository;

    public function __construct(OrderDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($orderId, $projectId, $displayUid, $sourceId, $languageId, $marketCountry, $action, $createdUserId): void
    {
        $orderDataLanguage = OrderDataLanguage::create($orderId, $languageId, $projectId, $action);
        $orderDataMarketCountry = OrderDataMarketCountry::create($orderId, $marketCountry, $projectId, $action);

        $orderData = OrderData::create(
            $orderId,
            $displayUid,
            $sourceId,
            $orderDataLanguage->getValue(),
            $orderDataMarketCountry->getValue(),
            $createdUserId
        );

        $orderData->detachBehavior('user');

        $this->repository->save($orderData);
    }
}
