<?php

namespace sales\model\voiceMailRecord\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\voiceMailRecord\entity\VoiceMailRecord;

class VoiceMailRecordSearch extends VoiceMailRecord
{
    public function rules(): array
    {
        return [
            ['vmr_call_id', 'integer'],

            ['vmr_client_id', 'integer'],

            ['vmr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['vmr_deleted', 'boolean'],

            ['vmr_duration', 'integer'],

            ['vmr_new', 'boolean'],

            ['vmr_record_sid', 'string'],

            ['vmr_user_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()->with(['user', 'client']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'vmr_call_id' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->vmr_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'vmr_created_dt', $this->vmr_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'vmr_call_id' => $this->vmr_call_id,
            'vmr_client_id' => $this->vmr_client_id,
            'vmr_user_id' => $this->vmr_user_id,
            'vmr_duration' => $this->vmr_duration,
            'vmr_new' => $this->vmr_new,
            'vmr_deleted' => $this->vmr_deleted,
            'vmr_record_sid' => $this->vmr_record_sid,
        ]);

        return $dataProvider;
    }

    public function list($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()->with(['user', 'client']);
        $query
            ->andWhere(['vmr_user_id' => $user->id])
            ->andWhere(['vmr_deleted' => false]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'vmr_call_id' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->vmr_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'vmr_created_dt', $this->vmr_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'vmr_call_id' => $this->vmr_call_id,
            'vmr_client_id' => $this->vmr_client_id,
            'vmr_duration' => $this->vmr_duration,
            'vmr_new' => $this->vmr_new,
            'vmr_record_sid' => $this->vmr_record_sid,
        ]);

        return $dataProvider;
    }
}
