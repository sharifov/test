<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DepartmentPhoneProject;

/**
 * DepartmentPhoneProjectSearch represents the model behind the search form of `common\models\DepartmentPhoneProject`.
 */
class DepartmentPhoneProjectSearch extends DepartmentPhoneProject
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dpp_dep_id', 'dpp_project_id', 'dpp_source_id', 'dpp_avr_enable', 'dpp_enable', 'dpp_updated_user_id'], 'integer'],
            [['dpp_phone_number', 'dpp_params', 'dpp_updated_dt'], 'safe'],
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
        $query = DepartmentPhoneProject::find();

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
            'dpp_dep_id' => $this->dpp_dep_id,
            'dpp_project_id' => $this->dpp_project_id,
            'dpp_source_id' => $this->dpp_source_id,
            'dpp_avr_enable' => $this->dpp_avr_enable,
            'dpp_enable' => $this->dpp_enable,
            'dpp_updated_user_id' => $this->dpp_updated_user_id,
            'dpp_updated_dt' => $this->dpp_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'dpp_phone_number', $this->dpp_phone_number])
            ->andFilterWhere(['like', 'dpp_params', $this->dpp_params]);

        return $dataProvider;
    }
}
