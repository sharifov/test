<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;

/**
 * ProjectSearch represents the model behind the search form of `common\models\Project`.
 */
class ProjectSearch extends Project
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'closed'], 'integer'],
            [['name', 'link', 'api_key', 'contact_info', 'last_update', 'custom_data'], 'safe'],
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
        $query = Project::find();

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

        if ($this->last_update) {
            $query->andFilterWhere(['>=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update))])
                ->andFilterWhere(['<=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'closed' => $this->closed,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'contact_info', $this->contact_info])
            ->andFilterWhere(['like', 'custom_data', $this->custom_data]);

        return $dataProvider;
    }
}
