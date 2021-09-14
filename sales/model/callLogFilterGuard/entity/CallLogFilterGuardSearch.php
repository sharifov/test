<?php

namespace sales\model\callLogFilterGuard\entity;

use yii\data\ActiveDataProvider;
use sales\model\callLogFilterGuard\entity\CallLogFilterGuard;

class CallLogFilterGuardSearch extends CallLogFilterGuard
{
    public function rules(): array
    {
        return [
            ['clfg_call_id', 'integer'],

            ['clfg_sd_rate', 'number'],

            ['clfg_trust_percent', 'integer'],

            ['clfg_type', 'integer'],

            ['clfg_created_dt', 'date', 'format' => 'php:Y-m-d'],

            [['clfg_cpl_id'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['clfg_call_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'clfg_call_id' => $this->clfg_call_id,
            'clfg_type' => $this->clfg_type,
            'clfg_sd_rate' => $this->clfg_sd_rate,
            'clfg_trust_percent' => $this->clfg_trust_percent,
            'clfg_cpl_id' => $this->clfg_cpl_id,
            'DATE(clfg_created_dt)' => $this->clfg_created_dt,
        ]);

        return $dataProvider;
    }
}
