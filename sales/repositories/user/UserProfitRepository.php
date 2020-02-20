<?php
namespace sales\repositories\user;

use sales\model\user\entity\payroll\UserPayroll;
use sales\model\user\entity\profit\search\UserProfitSearch;
use sales\model\user\entity\profit\UserProfit;
use sales\model\user\entity\profit\UserProfitQuery;
use sales\repositories\Repository;

class UserProfitRepository extends Repository
{
	/**
	 * @param string $date
	 * @param int|null $userId
	 * @return array|UserProfitSearch[]
	 */
	public function getDataForCalcUserPayroll(string $date, int $userId = null): array
	{
		$data = UserProfitSearch::searchForCalcPayroll($date, $userId);
		if (empty($data)) {
			throw new \RuntimeException('Not Found data for calc payroll; Provide another date');
		}
		return $data;
	}

	/**
	 * @param UserProfit $userProfit
	 * @return int
	 */
	public function save(UserProfit $userProfit): int
	{
		if (!$userProfit->save()) {
			throw new \RuntimeException('User profit saving error');
		}
		return $userProfit->up_id;
	}

	/**
	 * @param UserPayroll $userPayroll
	 */
	public function linkPayroll(UserPayroll $userPayroll): void
	{
		$date = date('Y-m', strtotime($userPayroll->ups_year .'-'.$userPayroll->ups_month));
		foreach ($this->findByDateAndUserId($date, $userPayroll->ups_user_id) as $userProfit) {
			$userProfit->link('upPayroll', $userPayroll);
		}
	}

	/**
	 * @param string $date
	 * @param int $userId
	 * @return array|UserProfitSearch[]|UserProfit[]
	 */
	public function findByDateAndUserId(string $date, int $userId): array
	{
		$userProfits = UserProfit::find()->where(['date_format(up_created_dt, "%Y-%m")' => $date])->andWhere(['up_user_id' => $userId]);
		if (!$userProfits) {
			throw new \RuntimeException('Not found Profits by date and user');
		}
		return $userProfits->all();
	}

	/**
	 * @param int $userId
	 * @param int $orderId
	 * @param int $productQuoteId
	 * @return UserProfit
	 */
	public function findOrCreate(int $userId, int $orderId, int $productQuoteId): UserProfit
	{
		$userProfit = UserProfit::findOne(['up_user_id' => $userId, 'up_order_id' => $orderId, 'up_product_quote_id' => $productQuoteId]);
		if ($userProfit === null) {
			$userProfit = new UserProfit();
		}
		return $userProfit;
	}
}