<?php

namespace sales\model\leadProduct\entity\search;

use sales\model\leadProduct\entity\LeadProduct;
use yii\data\ActiveDataProvider;

class LeadProductSearch extends LeadProduct
{
    public function rules(): array
    {
        return [
            ['lp_lead_id', 'integer'],

            ['lp_product_id', 'integer'],
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
            'lp_lead_id' => $this->lp_lead_id,
            'lp_product_id' => $this->lp_product_id,
        ]);

        return $dataProvider;
    }
}
