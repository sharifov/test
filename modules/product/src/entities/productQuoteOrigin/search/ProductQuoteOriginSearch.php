<?php

namespace modules\product\src\entities\productQuoteOrigin\search;

use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin;

class ProductQuoteOriginSearch extends ProductQuoteOrigin
{
    public function rules(): array
    {
        return [
            ['pqa_product_id', 'integer'],

            ['pqa_quote_id', 'integer'],
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
            'pqa_product_id' => $this->pqa_product_id,
            'pqa_quote_id' => $this->pqa_quote_id,
        ]);

        return $dataProvider;
    }
}
