<?php

namespace src\model\callLogFilterGuard\entity;

use common\models\Call;
use yii\data\ActiveDataProvider;

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

            ['clfg_call_log_id', 'integer'],

            ['clfg_redial_status', 'integer'],
            ['clfg_redial_status', 'in', 'range' => array_keys(Call::STATUS_LIST)],

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
            'clfg_call_log_id' => $this->clfg_call_log_id,
            'clfg_redial_status' => $this->clfg_redial_status,
        ]);

        return $dataProvider;
    }
}
