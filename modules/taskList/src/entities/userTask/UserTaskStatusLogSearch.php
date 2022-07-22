<?php

namespace modules\taskList\src\entities\userTask;

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
            [['utsl_description', 'utsl_created_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'utsl_id' => $this->utsl_id,
            'utsl_ut_id' => $this->utsl_ut_id,
            'utsl_old_status' => $this->utsl_old_status,
            'utsl_new_status' => $this->utsl_new_status,
            'utsl_created_user_id' => $this->utsl_created_user_id,
            'utsl_created_dt' => $this->utsl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'utsl_description', $this->utsl_description]);

        return $dataProvider;
    }
}
