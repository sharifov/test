<?php
namespace sales\repositories\user;

use sales\model\user\entity\payroll\UserPayroll;
use sales\repositories\Repository;

class UserPayrollRepository extends Repository
{
	/**
	 * @param UserPayroll $userPayroll
	 * @return int
	 */
	public function save(UserPayroll $userPayroll): int
	{
		if (!$userPayroll->save()) {
			throw new \RuntimeException($userPayroll->getErrorSummary(false)[0]);
		}
		return $userPayroll->ups_id;
	}

	/**
	 * @param int $id
	 * @return UserPayroll
	 */
	public function findOneById(int $id): UserPayroll
	{
		$payroll = UserPayroll::findOne($id);
		if ($payroll === null) {
			throw new \RuntimeException('Not found user payroll');
		}
		return $payroll;
	}
}