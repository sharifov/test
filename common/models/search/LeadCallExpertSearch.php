<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadCallExpert;

/**
 * LeadCallExpertSearch represents the model behind the search form of `common\models\LeadCallExpert`.
 */
class LeadCallExpertSearch extends LeadCallExpert
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lce_id', 'lce_lead_id', 'lce_status_id', 'lce_agent_user_id', 'lce_expert_user_id'], 'integer'],
            [['lce_request_text', 'lce_request_dt', 'lce_response_text', 'lce_response_lead_quotes', 'lce_response_dt', 'lce_expert_username', 'lce_updated_dt'], 'safe'],
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
        $query = LeadCallExpert::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lce_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lce_id' => $this->lce_id,
            'lce_lead_id' => $this->lce_lead_id,
            'lce_request_dt' => $this->lce_request_dt,
            'lce_response_dt' => $this->lce_response_dt,
            'lce_status_id' => $this->lce_status_id,
            'lce_agent_user_id' => $this->lce_agent_user_id,
            'lce_expert_user_id' => $this->lce_expert_user_id,
            'lce_updated_dt' => $this->lce_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'lce_request_text', $this->lce_request_text])
            ->andFilterWhere(['like', 'lce_response_text', $this->lce_response_text])
            ->andFilterWhere(['like', 'lce_response_lead_quotes', $this->lce_response_lead_quotes])
            ->andFilterWhere(['like', 'lce_expert_username', $this->lce_expert_username]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByLead($params)
    {
        $query = LeadCallExpert::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lce_id' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lce_id' => $this->lce_id,
            'lce_lead_id' => $this->lce_lead_id,
            'lce_request_dt' => $this->lce_request_dt,
            'lce_response_dt' => $this->lce_response_dt,
            'lce_status_id' => $this->lce_status_id,
            'lce_agent_user_id' => $this->lce_agent_user_id,
            'lce_expert_user_id' => $this->lce_expert_user_id,
            'lce_updated_dt' => $this->lce_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'lce_request_text', $this->lce_request_text])
            ->andFilterWhere(['like', 'lce_response_text', $this->lce_response_text])
            ->andFilterWhere(['like', 'lce_response_lead_quotes', $this->lce_response_lead_quotes])
            ->andFilterWhere(['like', 'lce_expert_username', $this->lce_expert_username]);

        return $dataProvider;
    }

}
