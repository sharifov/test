<?php

namespace modules\product\src\entities\productQuoteChangeRelation;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;

/**
 * Class ProductQuoteChangeRelationSearch
 */
class ProductQuoteChangeRelationSearch extends ProductQuoteChangeRelation
{
    public function rules(): array
    {
        return [
            ['pqcr_pq_id', 'integer'],

            ['pqcr_pqc_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pqcr_pq_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pqcr_pqc_id' => $this->pqcr_pqc_id,
            'pqcr_pq_id' => $this->pqcr_pq_id,
        ]);

        return $dataProvider;
    }
}
