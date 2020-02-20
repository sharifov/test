<?php
namespace sales\model\user\entity\profit\service;

/**
 * Class OrderUserProfitCreateUpdateDTO
 * @package sales\model\user\entity\profit\service
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
	private $userId;
	/**
	 * @var int|null
	 */
	private $leadId;
	/**
	 * @var int|null
	 */
	private $orderId;
	/**
	 * @var int|null
	 */
	private $productQuoteId;
	/**
	 * @var int|null
	 */
	private $percent;
	/**
	 * @var float|null
	 */
	private $profit;
	/**
	 * @var int|null
	 */
	private $splitPercent;
	/**
	 * @var int|null
	 */
	private $statusId;
	/**
	 * @var int|null
	 */
	private $payrollId;
	/**
	 * @var int|null
	 */
	private $typeId;

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