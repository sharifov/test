<?php

namespace sales\model\conference\entity\conferenceEventLog\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLog;

class ConferenceEventLogSearch extends ConferenceEventLog
{
    public function rules(): array
    {
        return [
            ['cel_conference_sid', 'string'],

            ['cel_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cel_event_type', 'string'],

            ['cel_id', 'integer'],

            ['cel_sequence_number', 'integer'],
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

        if ($this->cel_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cel_created_dt', $this->cel_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cel_id' => $this->cel_id,
            'cel_sequence_number' => $this->cel_sequence_number,
            'cel_event_type' => $this->cel_event_type,
            'cel_conference_sid' => $this->cel_conference_sid,
        ]);

        return $dataProvider;
    }
}
