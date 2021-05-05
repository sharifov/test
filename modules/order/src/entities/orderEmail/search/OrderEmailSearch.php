<?php

namespace modules\order\src\entities\orderEmail\search;

use yii\data\ActiveDataProvider;
use modules\order\src\entities\orderEmail\OrderEmail;

class OrderEmailSearch extends OrderEmail
{
    public function rules(): array
    {
        return [
            ['oe_created_dt', 'safe'],

            ['oe_email_id', 'integer'],

            ['oe_id', 'integer'],

            ['oe_order_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'oe_order_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'oe_id' => $this->oe_id,
            'oe_order_id' => $this->oe_order_id,
            'oe_email_id' => $this->oe_email_id,
            'date(oe_created_dt)' => $this->oe_created_dt,
        ]);

        return $dataProvider;
    }
}
