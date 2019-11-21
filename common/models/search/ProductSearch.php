<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_id', 'pr_type_id', 'pr_lead_id', 'pr_status_id', 'pr_created_user_id', 'pr_updated_user_id'], 'integer'],
            [['pr_name', 'pr_description', 'pr_created_dt', 'pr_updated_dt'], 'safe'],
            [['pr_service_fee_percent'], 'number'],
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
        $query = Product::find();

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
            'pr_id' => $this->pr_id,
            'pr_type_id' => $this->pr_type_id,
            'pr_lead_id' => $this->pr_lead_id,
            'pr_status_id' => $this->pr_status_id,
            'pr_service_fee_percent' => $this->pr_service_fee_percent,
            'pr_created_user_id' => $this->pr_created_user_id,
            'pr_updated_user_id' => $this->pr_updated_user_id,
            'pr_created_dt' => $this->pr_created_dt,
            'pr_updated_dt' => $this->pr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pr_name', $this->pr_name])
            ->andFilterWhere(['like', 'pr_description', $this->pr_description]);

        return $dataProvider;
    }
}
