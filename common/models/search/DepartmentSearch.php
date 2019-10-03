<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Department;

/**
 * DepartmentSearch represents the model behind the search form of `common\models\Department`.
 */
class DepartmentSearch extends Department
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dep_id', 'dep_updated_user_id'], 'integer'],
            [['dep_key', 'dep_name', 'dep_updated_dt'], 'safe'],
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
        $query = Department::find();

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

        if ($this->dep_updated_dt) {
            $query->andFilterWhere(['>=', 'dep_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->dep_updated_dt))])
                ->andFilterWhere(['<=', 'dep_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->dep_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'dep_id' => $this->dep_id,
            'dep_updated_user_id' => $this->dep_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'dep_key', $this->dep_key])
            ->andFilterWhere(['like', 'dep_name', $this->dep_name]);

        return $dataProvider;
    }
}
