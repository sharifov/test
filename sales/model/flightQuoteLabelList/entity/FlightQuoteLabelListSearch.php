<?php

namespace sales\model\flightQuoteLabelList\entity;

use yii\data\ActiveDataProvider;
use sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList;

/**
 * Class FlightQuoteLabelSearch
 */
class FlightQuoteLabelListSearch extends FlightQuoteLabelList
{
    public function rules(): array
    {
        return [
            ['fqll_created_user_id', 'integer'],

            [['fqll_label_key', 'fqll_description', 'fqll_origin_description'], 'string'],

            ['fqll_id', 'integer'],

            [['fqll_created_dt', 'fqll_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqll_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fqll_id' => $this->fqll_id,
            'DATE(fqll_created_dt)' => $this->fqll_created_dt,
            'DATE(fqll_updated_dt)' => $this->fqll_updated_dt,
            'fqll_created_user_id' => $this->fqll_created_user_id,
            'fqll_updated_user_id' => $this->fqll_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'fqll_label_key', $this->fqll_label_key])
            ->andFilterWhere(['like', 'fqll_origin_description', $this->fqll_origin_description])
            ->andFilterWhere(['like', 'fqll_description', $this->fqll_description]);

        return $dataProvider;
    }
}
