<?php
namespace sales\services\user\payroll;

use sales\model\user\entity\payroll\UserPayroll;
use sales\model\user\entity\profit\search\UserProfitSearch;

class UserPayrollCreateDTO
{
	public $userId;
	public $month;
	public $year;
	public $baseAmount;
	public $profitAmount;
	public $taxAmount;
	public $paymentAmount;
	public $agentStatus;
	public $status;

	/**
	 * @param UserProfitSearch $userProfitSearch
	 * @return UserPayrollCreateDTO
	 */
	public function feelByUserPayrollSearch(UserProfitSearch $userProfitSearch): UserPayrollCreateDTO
	{
		$this->userId = $userProfitSearch->up_user_id;
		$this->month = date('n', strtotime($userProfitSearch->date));
		$this->year = date('Y', strtotime($userProfitSearch->date));
		$this->baseAmount = $userProfitSearch->base_amount;
		$this->profitAmount = $userProfitSearch->sum_profit_amount;
		$this->taxAmount = null;
		$this->paymentAmount = $userProfitSearch->sum_payment_amount;
		$this->agentStatus = UserPayroll::AGENT_STATUS_PENDING;
		$this->status = UserPayroll::STATUS_PENDING;

		return $this;
	}
}