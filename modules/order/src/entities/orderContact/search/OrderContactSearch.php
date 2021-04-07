<?php

namespace modules\order\src\entities\orderContact\search;

use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderContact\OrderContact;

class OrderContactSearch extends OrderContact
{
    public function rules(): array
    {
        return [
            ['oc_created_dt', 'safe'],

            ['oc_email', 'safe'],

            ['oc_first_name', 'safe'],

            ['oc_id', 'integer'],

            ['oc_last_name', 'safe'],

            ['oc_middle_name', 'safe'],

            ['oc_order_id', 'integer'],

            ['oc_phone_number', 'safe'],

            ['oc_updated_dt', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'oc_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'oc_id' => $this->oc_id,
            'oc_order_id' => $this->oc_order_id,
            'oc_created_dt' => $this->oc_created_dt,
            'oc_updated_dt' => $this->oc_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'oc_first_name', $this->oc_first_name])
            ->andFilterWhere(['like', 'oc_last_name', $this->oc_last_name])
            ->andFilterWhere(['like', 'oc_middle_name', $this->oc_middle_name])
            ->andFilterWhere(['like', 'oc_email', $this->oc_email])
            ->andFilterWhere(['like', 'oc_phone_number', $this->oc_phone_number]);

        return $dataProvider;
    }
}
