<?php

namespace sales\model\quoteLabel\entity;

use yii\data\ActiveDataProvider;
use sales\model\quoteLabel\entity\QuoteLabel;

class QuoteLabelSearch extends QuoteLabel
{
    public function rules(): array
    {
        return [
            ['ql_label_key', 'string'],

            ['ql_quote_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ql_quote_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ql_quote_id' => $this->ql_quote_id,
        ]);

        $query->andFilterWhere(['like', 'ql_label_key', $this->ql_label_key]);

        return $dataProvider;
    }
}
