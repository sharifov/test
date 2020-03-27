<?php
namespace common\models\query;

use yii\db\ActiveRecord;

/**
 * Class UserCommissionRulesQuery
 * @package common\models\query
 *
 * @see \common\models\UserCommissionRules
 */
class UserCommissionRulesQuery extends \yii\db\ActiveQuery
{
	/**
	 * @param int $exp
	 * @param float $kpiPercent
	 * @param int $orderProfit
	 * @return int|array|ActiveRecord
	 */
	public function getCommissionValue(int $exp, float $kpiPercent, int $orderProfit)
	{
		$value = $this
			->andWhere(['user_commission_rules.ucr_exp_month' => $exp])
			->andWhere(['user_commission_rules.ucr_kpi_percent' => $kpiPercent])
			->andWhere(['user_commission_rules.ucr_order_profit' => $orderProfit])
			->orderBy(['ucr_exp_month' => SORT_DESC, 'ucr_kpi_percent' => SORT_DESC, 'ucr_order_profit' => SORT_DESC])->one();

		return $value ?? 0;
	}
}