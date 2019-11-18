<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OfferProduct;

/**
 * OfferProductSearch represents the model behind the search form of `common\models\OfferProduct`.
 */
class OfferProductSearch extends OfferProduct
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['op_offer_id', 'op_product_quote_id', 'op_created_user_id'], 'integer'],
            [['op_created_dt'], 'safe'],
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
        $query = OfferProduct::find();

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
            'op_offer_id' => $this->op_offer_id,
            'op_product_quote_id' => $this->op_product_quote_id,
            'op_created_user_id' => $this->op_created_user_id,
            'op_created_dt' => $this->op_created_dt,
        ]);

        return $dataProvider;
    }
}
