<?php

namespace modules\product\src\entities\productQuoteChange\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;

/**
 * ProductQuoteChangeSearch represents the model behind the search form of `modules\product\src\entities\productQuoteChange\ProductQuoteChange`.
 */
class ProductQuoteChangeSearch extends ProductQuoteChange
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqc_id', 'pqc_pq_id', 'pqc_case_id', 'pqc_decision_user', 'pqc_status_id', 'pqc_decision_type_id'], 'integer'],
            [['pqc_created_dt', 'pqc_updated_dt', 'pqc_decision_dt'], 'safe'],
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
        $query = ProductQuoteChange::find();

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
            'pqc_id' => $this->pqc_id,
            'pqc_pq_id' => $this->pqc_pq_id,
            'pqc_case_id' => $this->pqc_case_id,
            'pqc_decision_user' => $this->pqc_decision_user,
            'pqc_status_id' => $this->pqc_status_id,
            'pqc_decision_type_id' => $this->pqc_decision_type_id,
            'date(pqc_created_dt)' => $this->pqc_created_dt,
            'date(pqc_updated_dt)' => $this->pqc_updated_dt,
            'date(pqc_decision_dt)' => $this->pqc_decision_dt,
        ]);

        return $dataProvider;
    }
}
