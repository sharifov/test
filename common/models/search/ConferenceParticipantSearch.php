<?php

namespace common\models\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use common\models\ConferenceParticipant;

/**
 * ConferenceParticipantSearch represents the model behind the search form of `common\models\ConferenceParticipant`.
 */
class ConferenceParticipantSearch extends ConferenceParticipant
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cp_id', 'cp_cf_id', 'cp_call_id', 'cp_status_id'], 'integer'],
            [['cp_call_sid'], 'safe'],

            ['cp_type_id', 'integer'],

            [['cp_leave_dt', 'cp_join_dt', 'cp_hold_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cp_user_id', 'integer'],
            ['cp_identity', 'string'],
        ];
    }

    public function search($params, Employee $user)
    {
        $query = ConferenceParticipant::find()->with('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cp_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cp_join_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cp_join_dt', $this->cp_join_dt, $user->timezone);
        }

        if ($this->cp_leave_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cp_leave_dt', $this->cp_leave_dt, $user->timezone);
        }

        if ($this->cp_hold_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cp_hold_dt', $this->cp_hold_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cp_id' => $this->cp_id,
            'cp_cf_id' => $this->cp_cf_id,
            'cp_call_id' => $this->cp_call_id,
            'cp_status_id' => $this->cp_status_id,
            'cp_type_id' => $this->cp_type_id,
            'cp_user_id' => $this->cp_user_id,
            'cp_identity' => $this->cp_identity,
        ]);

        $query->andFilterWhere(['like', 'cp_call_sid', $this->cp_call_sid]);

        return $dataProvider;
    }
}
