<?php

namespace sales\model\conference\entity\conferenceParticipantStats\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats;

class ConferenceParticipantStatsSearch extends ConferenceParticipantStats
{
    public function rules(): array
    {
        return [
            ['cps_cf_id', 'integer'],

            ['cps_cf_sid', 'string'],

            ['cps_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cps_duration', 'integer'],

            ['cps_hold_time', 'integer'],

            ['cps_id', 'integer'],

            ['cps_participant_identity', 'string'],

            ['cps_talk_time', 'integer'],

            ['cps_user_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
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

        if ($this->cps_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cps_created_dt', $this->cps_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cps_id' => $this->cps_id,
            'cps_cf_id' => $this->cps_cf_id,
            'cps_user_id' => $this->cps_user_id,
            'cps_duration' => $this->cps_duration,
            'cps_talk_time' => $this->cps_talk_time,
            'cps_hold_time' => $this->cps_hold_time,
            'cps_cf_sid' => $this->cps_cf_sid,
            'cps_participant_identity' => $this->cps_participant_identity,
        ]);

        return $dataProvider;
    }
}
