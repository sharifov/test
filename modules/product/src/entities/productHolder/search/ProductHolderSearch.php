<?php

namespace modules\product\src\entities\productHolder\search;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productHolder\ProductHolder;

class ProductHolderSearch extends ProductHolder
{
    public function rules(): array
    {
        return [
            ['ph_created_dt', 'safe'],

            ['ph_email', 'safe'],

            ['ph_first_name', 'safe'],

            ['ph_id', 'integer'],

            ['ph_last_name', 'safe'],

            ['ph_phone_number', 'safe'],

            ['ph_product_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ph_id' => $this->ph_id,
            'ph_product_id' => $this->ph_product_id,
            'ph_created_dt' => $this->ph_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ph_first_name', $this->ph_first_name])
            ->andFilterWhere(['like', 'ph_last_name', $this->ph_last_name])
            ->andFilterWhere(['like', 'ph_email', $this->ph_email])
            ->andFilterWhere(['like', 'ph_phone_number', $this->ph_phone_number]);

        return $dataProvider;
    }
}
