<?php

namespace modules\objectTask\src\entities;

use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * ObjectTaskSearch represents the model behind the search form of `modules\objectTask\src\entities\ObjectTask`.
 */
class ObjectTaskSearch extends ObjectTask
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ot_uuid', 'ot_object', 'ot_execution_dt', 'ot_command', 'ot_created_dt', 'ot_group_hash'], 'safe'],
            [['ot_q_id', 'ot_object_id', 'ot_status'], 'integer'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = ObjectTask::find()
            ->orderBy([
                'ot_execution_dt' => SORT_ASC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->ot_execution_dt) {
            $query->andFilterWhere(['>=', 'ot_execution_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ot_execution_dt))])
                ->andFilterWhere(['<=', 'ot_execution_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ot_execution_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ot_q_id' => $this->ot_q_id,
            'ot_object_id' => $this->ot_object_id,
            'ot_status' => $this->ot_status,
        ]);

        $query->andFilterWhere(['like', 'ot_uuid', $this->ot_uuid])
            ->andFilterWhere(['like', 'ot_object', $this->ot_object])
            ->andFilterWhere(['ot_group_hash' => $this->ot_group_hash])
            ->andFilterWhere(['like', 'ot_command', $this->ot_command]);

        return $dataProvider;
    }
}
