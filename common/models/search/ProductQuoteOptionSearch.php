<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductQuoteOption;

/**
 * ProductQuoteOptionSearch represents the model behind the search form of `common\models\ProductQuoteOption`.
 */
class ProductQuoteOptionSearch extends ProductQuoteOption
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqo_id', 'pqo_product_quote_id', 'pqo_product_option_id', 'pqo_status_id', 'pqo_created_user_id', 'pqo_updated_user_id'], 'integer'],
            [['pqo_name', 'pqo_description', 'pqo_created_dt', 'pqo_updated_dt'], 'safe'],
            [['pqo_price', 'pqo_client_price', 'pqo_extra_markup'], 'number'],
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
        $query = ProductQuoteOption::find();

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
            'pqo_id' => $this->pqo_id,
            'pqo_product_quote_id' => $this->pqo_product_quote_id,
            'pqo_product_option_id' => $this->pqo_product_option_id,
            'pqo_status_id' => $this->pqo_status_id,
            'pqo_price' => $this->pqo_price,
            'pqo_client_price' => $this->pqo_client_price,
            'pqo_extra_markup' => $this->pqo_extra_markup,
            'pqo_created_user_id' => $this->pqo_created_user_id,
            'pqo_updated_user_id' => $this->pqo_updated_user_id,
            'pqo_created_dt' => $this->pqo_created_dt,
            'pqo_updated_dt' => $this->pqo_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pqo_name', $this->pqo_name])
            ->andFilterWhere(['like', 'pqo_description', $this->pqo_description]);

        return $dataProvider;
    }
}
