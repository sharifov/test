<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;

/**
 * TaskSearch represents the model behind the search form of `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['t_id', 't_hidden', 't_category_id', 't_sort_order'], 'integer'],
            [['t_key', 't_name', 't_description'], 'safe'],
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
        $query = Task::find();

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
            't_id' => $this->t_id,
            't_hidden' => $this->t_hidden,
            't_category_id' => $this->t_category_id,
            't_sort_order' => $this->t_sort_order,
        ]);

        $query->andFilterWhere(['like', 't_key', $this->t_key])
            ->andFilterWhere(['like', 't_name', $this->t_name])
            ->andFilterWhere(['like', 't_description', $this->t_description]);

        return $dataProvider;
    }
}
