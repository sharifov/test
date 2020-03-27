<?php
namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserBonusRulesQuery
 * @package common\models\query
 *
 * @see \common\models\UserBonusRules
 */
class UserBonusRulesQuery extends ActiveQuery
{
	/**
	 * @param int $exp
	 * @param float $kpiPercent
	 * @param int $orderProfit
	 * @return array|int|\yii\db\ActiveRecord
	 */
	public function getBonusValue(int $exp, float $kpiPercent, int $orderProfit)
	{
		$value = $this
			->andWhere(['user_bonus_rules.ubr_exp_month' => $exp])
			->andWhere(['user_bonus_rules.ubr_kpi_percent' => $kpiPercent])
			->andWhere(['user_bonus_rules.ubr_order_profit' => $orderProfit])
			->orderBy(['ubr_exp_month' => SORT_DESC, 'ubr_kpi_percent' => SORT_DESC, 'ubr_order_profit' => SORT_DESC])->one();

		return $value ?? 0;
	}
}