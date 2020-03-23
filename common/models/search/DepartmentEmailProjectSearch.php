<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DepartmentEmailProject;

/**
 * DepartmentEmailProjectSearch represents the model behind the search form of `common\models\DepartmentEmailProject`.
 */
class DepartmentEmailProjectSearch extends DepartmentEmailProject
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dep_id', 'dep_project_id', 'dep_dep_id', 'dep_source_id', 'dep_enable', 'dep_updated_user_id', 'dep_default'], 'integer'],
            [['dep_email', 'dep_updated_dt'], 'safe'],

            ['dep_email_list_id', 'integer'],
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
        $query = DepartmentEmailProject::find()->with(['emailList']);

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
            'dep_id' => $this->dep_id,
            'dep_project_id' => $this->dep_project_id,
            'dep_dep_id' => $this->dep_dep_id,
            'dep_source_id' => $this->dep_source_id,
            'dep_enable' => $this->dep_enable,
            'dep_default' => $this->dep_default,
            'dep_updated_user_id' => $this->dep_updated_user_id,
            'dep_updated_dt' => $this->dep_updated_dt,
            'dep_email_list_id' => $this->dep_email_list_id,
        ]);

        $query->andFilterWhere(['like', 'dep_email', $this->dep_email]);

        return $dataProvider;
    }
}
