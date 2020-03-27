<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserCommissionRules;

/**
 * UserCommissionRulesSearch represents the model behind the search form of `common\models\UserCommissionRules`.
 */
class UserCommissionRulesSearch extends UserCommissionRules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit', 'ucr_created_user_id', 'ucr_updated_user_id'], 'integer'],
            [['ucr_value'], 'number'],
            [['ucr_created_dt', 'ucr_updated_dt'], 'safe'],
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
        $query = UserCommissionRules::find();

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
            'ucr_exp_month' => $this->ucr_exp_month,
            'ucr_kpi_percent' => $this->ucr_kpi_percent,
            'ucr_order_profit' => $this->ucr_order_profit,
            'ucr_value' => $this->ucr_value,
            'ucr_created_user_id' => $this->ucr_created_user_id,
            'ucr_updated_user_id' => $this->ucr_updated_user_id,
            'date_format(ucr_created_dt, "%Y-%m-%d")' => $this->ucr_created_dt,
            'date_format(ucr_updated_dt, "%Y-%m-%d")' => $this->ucr_updated_dt,
        ]);

        return $dataProvider;
    }
}
