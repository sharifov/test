<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;

/**
 * Class CreateOrderDTO
 * @package modules\order\src\services
 *
 * @property int $payStatus
 * @property int $status
 * @property int|null $leadId
 * @property array $requestData
 * @property string|null $clientCurrency
 * @property int|null $creationTypeId
 * @property int|null $requestId
 * @property int|null $projectId
 * @property string|null $fareId
 */
class CreateOrderDTO
{
    public $payStatus;

    public $status;

    public $leadId;

    public $requestData;

    public $clientCurrency;

    public $creationTypeId;

    public $requestId;

    public $projectId;

    public $fareId;

    public function __construct(
        ?int $leadId,
        ?string $clientCurrency = null,
        array $requestData = [],
        ?int $creationTypeId = null,
        ?int $requestId = null,
        ?int $projectId = null,
        ?int $status = OrderStatus::PENDING,
        ?string $fareId = null
    ) {
        $this->payStatus = OrderPayStatus::NOT_PAID;
        $this->status = $status;
        $this->leadId = $leadId;
        $this->requestData = $requestData;
        $this->clientCurrency = $clientCurrency;
        $this->creationTypeId = $creationTypeId;
        $this->requestId = $requestId;
        $this->projectId = $projectId;
        $this->fareId = $fareId;
    }
}
