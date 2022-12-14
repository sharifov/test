<?php

namespace modules\taskList\src\entities\taskList\search;

use modules\objectSegment\src\entities\ObjectSegmentTask;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\taskList\src\entities\taskList\TaskList;

/**
 * TaskListSearch represents the model behind the search form of `modules\taskList\src\entities\taskList\TaskList`.
 */
class TaskListSearch extends TaskList
{
    public $objectSegmentAssigned;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tl_id', 'tl_duration_min', 'tl_enable_type', 'tl_sort_order', 'tl_updated_user_id', 'tl_target_object_id'], 'integer'],
            [['tl_title', 'tl_object', 'tl_condition', 'tl_condition_json', 'tl_params_json', 'tl_cron_expression', 'tl_updated_dt', 'objectSegmentAssigned'], 'safe'],
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
        $query = TaskList::find();

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

        if (!empty($this->objectSegmentAssigned)) {
            $ostTableName = ObjectSegmentTask::tableName();
            $query->innerJoin(
                $ostTableName,
                "tl_id = {$ostTableName}.ostl_tl_id",
            )->andWhere([
                'IN', "{$ostTableName}.ostl_osl_id", $this->objectSegmentAssigned,
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tl_id' => $this->tl_id,
            'tl_target_object_id' => $this->tl_target_object_id,
            'tl_duration_min' => $this->tl_duration_min,
            'tl_enable_type' => $this->tl_enable_type,
            'tl_sort_order' => $this->tl_sort_order,
            'tl_updated_dt' => $this->tl_updated_dt,
            'tl_updated_user_id' => $this->tl_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'tl_title', $this->tl_title])
            ->andFilterWhere(['like', 'tl_object', $this->tl_object])
            ->andFilterWhere(['like', 'tl_condition', $this->tl_condition])
            ->andFilterWhere(['like', 'tl_condition_json', $this->tl_condition_json])
            ->andFilterWhere(['like', 'tl_params_json', $this->tl_params_json])
            ->andFilterWhere(['like', 'tl_cron_expression', $this->tl_cron_expression]);

        return $dataProvider;
    }
}
