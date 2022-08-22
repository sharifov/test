<?php

namespace modules\taskList\src\entities\userTask;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;

/**
 * UserTaskStatusLogSearch represents the model behind the search form of `modules\taskList\src\entities\userTask\UserTaskStatusLog`.
 */
class UserTaskStatusLogSearch extends UserTaskStatusLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utsl_id', 'utsl_ut_id', 'utsl_old_status', 'utsl_new_status', 'utsl_created_user_id'], 'integer'],
            [['utsl_description'], 'string'],
            [['utsl_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserTaskStatusLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['utsl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->utsl_created_dt) {
            $query->andFilterWhere(['>=', 'utsl_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->utsl_created_dt))])
                ->andFilterWhere(['<=', 'utsl_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->utsl_created_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'utsl_id' => $this->utsl_id,
            'utsl_ut_id' => $this->utsl_ut_id,
            'utsl_old_status' => $this->utsl_old_status,
            'utsl_new_status' => $this->utsl_new_status,
            'utsl_created_user_id' => $this->utsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'utsl_description', $this->utsl_description]);

        return $dataProvider;
    }
}
