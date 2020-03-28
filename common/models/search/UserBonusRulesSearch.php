<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserBonusRules;

/**
 * UserBonusRulesSearch represents the model behind the search form of `common\models\UserBonusRules`.
 */
class UserBonusRulesSearch extends UserBonusRules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit', 'ubr_created_user_id', 'ubr_updated_user_id'], 'integer'],
            [['ubr_value'], 'number'],
            [['ubr_created_dt', 'ubr_updated_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserBonusRules::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ubr_exp_month' => $this->ubr_exp_month,
            'ubr_kpi_percent' => $this->ubr_kpi_percent,
            'ubr_order_profit' => $this->ubr_order_profit,
            'ubr_value' => $this->ubr_value,
            'ubr_created_user_id' => $this->ubr_created_user_id,
            'ubr_updated_user_id' => $this->ubr_updated_user_id,
            'date(ubr_created_dt, "%Y-%m-%d")' => $this->ubr_created_dt,
            'date(ubr_updated_dt, "%Y-%m-%d")' => $this->ubr_updated_dt,
        ]);

        return $dataProvider;
    }

    public function getCommissionValue(int $exp, float $kpiPercent, int $orderProfit)
	{
		$query = UserBonusRules::find();
	}
}
