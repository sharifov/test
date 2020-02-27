<?php
namespace modules\order\src\services;

use modules\order\src\entities\order\OrderStatus;

class CreateOrderDTO
{
	public $status;

	public $leadId;

	public function __construct(int $leadId)
	{
		$this->status = OrderStatus::PENDING;
		$this->leadId = $leadId;
	}
}