<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductType;

/**
 * ProductTypeSearch represents the model behind the search form of `common\models\ProductType`.
 */
class ProductTypeSearch extends ProductType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pt_id', 'pt_enabled'], 'integer'],
            [['pt_service_fee_percent'], 'number'],
            [['pt_key', 'pt_name', 'pt_description', 'pt_settings', 'pt_created_dt', 'pt_updated_dt'], 'safe'],
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
        $query = ProductType::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['pt_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pt_id' => $this->pt_id,
            'pt_enabled' => $this->pt_enabled,
            'pt_service_fee_percent' => $this->pt_service_fee_percent,
            'pt_created_dt' => $this->pt_created_dt,
            'pt_updated_dt' => $this->pt_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pt_key', $this->pt_key])
            ->andFilterWhere(['like', 'pt_name', $this->pt_name])
            ->andFilterWhere(['like', 'pt_description', $this->pt_description])
            ->andFilterWhere(['like', 'pt_settings', $this->pt_settings]);

        return $dataProvider;
    }
}
