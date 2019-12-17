<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductOption;

/**
 * ProductOptionSearch represents the model behind the search form of `common\models\ProductOption`.
 */
class ProductOptionSearch extends ProductOption
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['po_id', 'po_product_type_id', 'po_price_type_id', 'po_enabled', 'po_created_user_id', 'po_updated_user_id'], 'integer'],
            [['po_key', 'po_name', 'po_description', 'po_created_dt', 'po_updated_dt'], 'safe'],
            [['po_max_price', 'po_min_price', 'po_price'], 'number'],
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
        $query = ProductOption::find();

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
            'po_id' => $this->po_id,
            'po_product_type_id' => $this->po_product_type_id,
            'po_price_type_id' => $this->po_price_type_id,
            'po_max_price' => $this->po_max_price,
            'po_min_price' => $this->po_min_price,
            'po_price' => $this->po_price,
            'po_enabled' => $this->po_enabled,
            'po_created_user_id' => $this->po_created_user_id,
            'po_updated_user_id' => $this->po_updated_user_id,
            'po_created_dt' => $this->po_created_dt,
            'po_updated_dt' => $this->po_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'po_key', $this->po_key])
            ->andFilterWhere(['like', 'po_name', $this->po_name])
            ->andFilterWhere(['like', 'po_description', $this->po_description]);

        return $dataProvider;
    }
}
