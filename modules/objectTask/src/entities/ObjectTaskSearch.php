<?php

namespace modules\objectTask\src\entities;

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
            [['ot_uuid', 'ot_object', 'execution_dt', 'ot_command', 'ot_created_dt'], 'safe'],
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
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ot_q_id' => $this->ot_q_id,
            'ot_object_id' => $this->ot_object_id,
            'ot_execution_dt' => $this->ot_execution_dt,
            'ot_status' => $this->ot_status,
            'ot_created_dt' => $this->ot_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ot_uuid', $this->ot_uuid])
            ->andFilterWhere(['like', 'ot_object', $this->ot_object])
            ->andFilterWhere(['like', 'ot_command', $this->ot_command]);

        return $dataProvider;
    }
}
