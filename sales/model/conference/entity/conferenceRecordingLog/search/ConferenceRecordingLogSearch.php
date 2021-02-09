<?php

namespace sales\model\conference\entity\conferenceRecordingLog\search;

use yii\data\ActiveDataProvider;
use sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog;

class ConferenceRecordingLogSearch extends ConferenceRecordingLog
{
    public function rules(): array
    {
        return [
            ['cfrl_conference_sid', 'safe'],

            ['cfrl_created_dt', 'safe'],

            ['cfrl_id', 'integer'],

            ['cfrl_month', 'integer'],

            ['cfrl_user_id', 'integer'],

            ['cfrl_year', 'integer'],
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
            'cfrl_id' => $this->cfrl_id,
            'cfrl_user_id' => $this->cfrl_user_id,
            'date(cfrl_created_dt)' => $this->cfrl_created_dt,
            'cfrl_year' => $this->cfrl_year,
            'cfrl_month' => $this->cfrl_month,
        ]);

        $query->andFilterWhere(['like', 'cfrl_conference_sid', $this->cfrl_conference_sid]);

        return $dataProvider;
    }
}
