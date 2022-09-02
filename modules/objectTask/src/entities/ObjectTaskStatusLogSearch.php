<?php

namespace modules\objectTask\src\entities;

use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * ObjectTaskStatusLogSearch represents the model behind the search form of `modules\objectTask\src\entities\ObjectTaskStatusLog`.
 */
class ObjectTaskStatusLogSearch extends ObjectTaskStatusLog
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['otsl_id', 'otsl_old_status', 'otsl_new_status', 'otsl_created_user_id'], 'integer'],
            [['otsl_ot_uuid', 'otsl_description'], 'safe'],
            [['otsl_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ObjectTaskStatusLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['otsl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->otsl_created_dt) {
            $query->andFilterWhere(['>=', 'otsl_created_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->otsl_created_dt))])
                ->andFilterWhere(['<=', 'otsl_created_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->otsl_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere([
            'otsl_id' => $this->otsl_id,
            'otsl_old_status' => $this->otsl_old_status,
            'otsl_new_status' => $this->otsl_new_status,
            'otsl_created_user_id' => $this->otsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'otsl_ot_uuid', $this->otsl_ot_uuid])
            ->andFilterWhere(['like', 'otsl_description', $this->otsl_description]);

        return $dataProvider;
    }
}
