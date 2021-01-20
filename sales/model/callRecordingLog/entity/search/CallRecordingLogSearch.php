<?php

namespace sales\model\callRecordingLog\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\callRecordingLog\entity\CallRecordingLog;

class CallRecordingLogSearch extends CallRecordingLog
{
    public function rules(): array
    {
        return [
            ['crl_call_sid', 'safe'],

            ['crl_created_dt', 'safe'],

            ['crl_id', 'integer'],

            ['crl_month', 'integer'],

            ['crl_user_id', 'integer'],

            ['crl_year', 'integer'],
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
            'crl_id' => $this->crl_id,
            'crl_user_id' => $this->crl_user_id,
            'date(crl_created_dt)' => $this->crl_created_dt,
            'crl_year' => $this->crl_year,
            'crl_month' => $this->crl_month,
        ]);

        $query->andFilterWhere(['like', 'crl_call_sid', $this->crl_call_sid]);

        return $dataProvider;
    }
}
