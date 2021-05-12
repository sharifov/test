<?php

namespace modules\product\src\entities\productQuoteLead\search;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteLead\ProductQuoteLead;

class ProductQuoteLeadSearch extends ProductQuoteLead
{
    public function rules(): array
    {
        return [
            ['pql_lead_id', 'integer'],

            ['pql_product_quote_id', 'integer'],
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
            'pql_product_quote_id' => $this->pql_product_quote_id,
            'pql_lead_id' => $this->pql_lead_id,
        ]);

        return $dataProvider;
    }
}
