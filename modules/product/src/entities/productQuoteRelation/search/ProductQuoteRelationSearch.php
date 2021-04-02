<?php

namespace modules\product\src\entities\productQuoteRelation\search;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\db\Expression;

class ProductQuoteRelationSearch extends ProductQuoteRelation
{
    public function rules(): array
    {
        return [
            ['pqr_created_dt',  'datetime', 'format' => 'php:Y-m-d'],

            ['pqr_created_user_id', 'integer'],

            ['pqr_parent_pq_id', 'integer'],

            ['pqr_related_pq_id', 'integer'],

            ['pqr_type_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pqr_created_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pqr_parent_pq_id' => $this->pqr_parent_pq_id,
            'pqr_related_pq_id' => $this->pqr_related_pq_id,
            'pqr_type_id' => $this->pqr_type_id,
            'pqr_created_user_id' => $this->pqr_created_user_id,
        ]);

        if ($this->pqr_created_dt) {
            $query->andWhere(new Expression(
                'DATE(pqr_created_dt) = :date',
                [':date' => date('Y-m-d', strtotime($this->pqr_created_dt))]
            ));
        }

        return $dataProvider;
    }
}
