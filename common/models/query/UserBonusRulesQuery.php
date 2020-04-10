<?php
namespace common\models\query;

use common\models\UserBonusRules;
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
	 * @return float
	 */
	public function getBonusValueByExpMonth(int $exp): float
	{
		/** @var $value UserBonusRules */
		$value = $this
			->andWhere(['>=', 'user_bonus_rules.ubr_exp_month', $exp])
			->orderBy(['ubr_exp_month' => SORT_ASC, 'ubr_kpi_percent' => SORT_DESC, 'ubr_order_profit' => SORT_DESC])->one();

		return $value ? $value->ubr_value : 0.00;
	}
}