<?php

namespace common\models\search;

use yii\data\ActiveDataProvider;
use common\models\ProjectWeight;

/**
 * ProjectWeightSearch represents the model behind the search form of `common\models\ProjectWeight`.
 */
class ProjectWeightSearch extends ProjectWeight
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['pw_project_id', 'pw_weight'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = ProjectWeight::find();

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
            'pw_project_id' => $this->pw_project_id,
            'pw_weight' => $this->pw_weight,
        ]);

        return $dataProvider;
    }
}
