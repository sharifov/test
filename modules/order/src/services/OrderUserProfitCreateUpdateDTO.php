<?php
namespace modules\order\src\services;

/**
 * Class OrderUserProfitCreateUpdateDTO
 * @package modules\order\src\services
 *
 * @property int|null $userId
 * @property int|null $leadId
 * @property int|null $orderId
 * @property int|null $productQuoteId
 * @property int|null $percent
 * @property float|null $profit
 * @property int|null $splitPercent
 * @property int|null $statusId
 * @property int|null $payrollId
 * @property int|null $typeId
 */
class OrderUserProfitCreateUpdateDTO
{
	/**
	 * @var int|null
	 */
	public $userId;
	/**
	 * @var int|null
	 */
	public $leadId;
	/**
	 * @var int|null
	 */
	public $orderId;
	/**
	 * @var int|null
	 */
	public $productQuoteId;
	/**
	 * @var int|null
	 */
	public $percent;
	/**
	 * @var float|null
	 */
	public $profit;
	/**
	 * @var int|null
	 */
	public $splitPercent;
	/**
	 * @var int|null
	 */
	public $statusId;
	/**
	 * @var int|null
	 */
	public $payrollId;
	/**
	 * @var int|null
	 */
	public $typeId;

	public function __construct(
		?int $userId,
		?int $leadId,
		?int $orderId,
		?int $productQuoteId,
		?int $percent,
		?float $profit,
		?int $splitPercent,
		?int $statusId = null,
		?int $payrollId = null,
		?int $typeId = null
	)
	{
		$this->userId = $userId;
		$this->leadId = $leadId;
		$this->orderId = $orderId;
		$this->productQuoteId = $productQuoteId;
		$this->percent = $percent;
		$this->profit = $profit;
		$this->splitPercent = $splitPercent;
		$this->statusId = $statusId;
		$this->payrollId = $payrollId;
		$this->typeId = $typeId;
	}
}