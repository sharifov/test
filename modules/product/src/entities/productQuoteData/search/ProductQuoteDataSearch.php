<?php

namespace modules\product\src\entities\productQuoteData\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteData\ProductQuoteData;

/**
 * ProductQuoteDataSearch represents the model behind the search form of `modules\product\src\entities\productQuoteData\ProductQuoteData`.
 */
class ProductQuoteDataSearch extends ProductQuoteData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqd_id', 'pqd_product_quote_id', 'pqd_key'], 'integer'],
            [['pqd_value', 'pqd_created_dt', 'pqd_updated_dt'], 'safe'],
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
        $query = ProductQuoteData::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pqd_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pqd_id' => $this->pqd_id,
            'pqd_product_quote_id' => $this->pqd_product_quote_id,
            'pqd_key' => $this->pqd_key,
            'date(pqd_created_dt)' => $this->pqd_created_dt,
            'date(pqd_updated_dt)' => $this->pqd_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pqd_value', $this->pqd_value]);

        return $dataProvider;
    }
}
