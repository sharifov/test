<?php

namespace modules\product\src\entities\productQuoteOrigin\search;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin;

class ProductQuoteOriginSearch extends ProductQuoteOrigin
{
    public function rules(): array
    {
        return [
            ['pqo_product_id', 'integer'],

            ['pqo_quote_id', 'integer'],
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
            'pqo_product_id' => $this->pqo_product_id,
            'pqo_quote_id' => $this->pqo_quote_id,
        ]);

        return $dataProvider;
    }
}
