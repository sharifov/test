<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OrderProduct;

/**
 * OrderProductSearch represents the model behind the search form of `common\models\OrderProduct`.
 */
class OrderProductSearch extends OrderProduct
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orp_order_id', 'orp_product_quote_id', 'orp_created_user_id'], 'integer'],
            [['orp_created_dt'], 'safe'],
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
        $query = OrderProduct::find();

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
            'orp_order_id' => $this->orp_order_id,
            'orp_product_quote_id' => $this->orp_product_quote_id,
            'orp_created_user_id' => $this->orp_created_user_id,
            'orp_created_dt' => $this->orp_created_dt,
        ]);

        return $dataProvider;
    }
}
