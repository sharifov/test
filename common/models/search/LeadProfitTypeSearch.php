<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadProfitType;

/**
 * LeadProfitTypeSearch represents the model behind the search form of `common\models\LeadProfitType`.
 */
class LeadProfitTypeSearch extends LeadProfitType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lpt_profit_type_id', 'lpt_diff_rule', 'lpt_commission_min', 'lpt_commission_max', 'lpt_commission_fix', 'lpt_created_user_id', 'lpt_updated_user_id'], 'integer'],
            [['lpt_created_dt', 'lpt_updated_dt'], 'safe'],
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
        $query = LeadProfitType::find();

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
            'lpt_profit_type_id' => $this->lpt_profit_type_id,
            'lpt_diff_rule' => $this->lpt_diff_rule,
            'lpt_commission_min' => $this->lpt_commission_min,
            'lpt_commission_max' => $this->lpt_commission_max,
            'lpt_commission_fix' => $this->lpt_commission_fix,
            'lpt_created_user_id' => $this->lpt_created_user_id,
            'lpt_updated_user_id' => $this->lpt_updated_user_id,
            'date_format(lpt_created_dt, "%Y-%m-%d")' => $this->lpt_created_dt,
            'date_format(lpt_updated_dt, "%Y-%m-%d")' => $this->lpt_updated_dt,
        ]);

        return $dataProvider;
    }
}
