<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ClientProject;

/**
 * ClientProjectSearch represents the model behind the search form of `common\models\ClientProject`.
 */
class ClientProjectSearch extends ClientProject
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cp_client_id', 'cp_project_id'], 'integer'],
            [['cp_created_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = ClientProject::find();

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
            'cp_client_id' => $this->cp_client_id,
            'cp_project_id' => $this->cp_project_id,
            'DATE(cp_created_dt)' => $this->cp_created_dt,
        ]);

        return $dataProvider;
    }
}
