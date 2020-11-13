<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleCreditCard;

/**
 * SaleCreditCardSearch represents the model behind the search form of `common\models\SaleCreditCard`.
 */
class SaleCreditCardSearch extends SaleCreditCard
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scc_sale_id', 'scc_cc_id', 'scc_created_user_id'], 'integer'],
            [['scc_created_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = SaleCreditCard::find();

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
            'scc_sale_id' => $this->scc_sale_id,
            'scc_cc_id' => $this->scc_cc_id,
            'DATE(scc_created_dt)' => $this->scc_created_dt,
            'scc_created_user_id' => $this->scc_created_user_id,
        ]);

        return $dataProvider;
    }
}
