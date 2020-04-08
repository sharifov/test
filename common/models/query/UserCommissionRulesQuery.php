<?php
namespace common\models\query;

use common\models\UserCommissionRules;
use yii\db\ActiveQuery;

/**
 * Class UserCommissionRulesQuery
 * @package common\models\query
 *
 * @see \common\models\UserCommissionRules
 */
class UserCommissionRulesQuery extends ActiveQuery
{
	/**
	 * @param int $exp
	 * @return float
	 */
	public function getCommissionValueByExpMonth(int $exp): float
	{
		/** @var $value UserCommissionRules */
		$value = $this
			->andWhere(['>=', 'user_commission_rules.ucr_exp_month', $exp])
			->orderBy(['ucr_exp_month' => SORT_ASC, 'ucr_kpi_percent' => SORT_DESC, 'ucr_order_profit' => SORT_DESC])->one();

		return $value ? $value->ucr_value : 0.00;
	}
}