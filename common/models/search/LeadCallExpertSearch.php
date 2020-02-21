<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadCallExpert;

/**
 * LeadCallExpertSearch represents the model behind the search form of `common\models\LeadCallExpert`.
 */
class LeadCallExpertSearch extends LeadCallExpert
{
    public $datetime_start;
    public $datetime_end;
    public $date_range;
    public $employeeRole;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end', 'employeeRole'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['lce_id', 'lce_lead_id', 'lce_status_id', 'lce_agent_user_id', 'lce_expert_user_id', 'lce_product_id'], 'integer'],
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
     * @throws \Exception
     */
    public function search($params)
    {
        $query = LeadCallExpert::find()->with('lceAgentUser');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lce_id' => SORT_DESC]],
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

        if (empty($this->lce_request_dt) && isset($params['LeadCallExpertSearch']['date_range'])) {
            $query->andFilterWhere(['>=', 'lce_request_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'lce_request_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if ($this->lce_request_dt) {
            $query->andFilterWhere(['>=', 'lce_request_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_request_dt))])
                ->andFilterWhere(['<=', 'lce_request_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_request_dt) + 3600 * 24)]);
        }

        if ($this->lce_response_dt) {
            $query->andFilterWhere(['>=', 'lce_response_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_response_dt))])
                ->andFilterWhere(['<=', 'lce_response_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_response_dt) + 3600 * 24)]);
        }

        if ($this->lce_updated_dt) {
            $query->andFilterWhere(['>=', 'lce_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_updated_dt))])
                ->andFilterWhere(['<=', 'lce_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lce_updated_dt) + 3600 * 24)]);
        }

        if (!empty($this->employeeRole)) {
            $subQuery = Employee::find()->select(['id'])->leftJoin('auth_assignment', 'auth_assignment.user_id = id')
                ->andWhere(['auth_assignment.item_name' => $this->employeeRole]);
            $query->andWhere(['IN', 'lce_agent_user_id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lce_id' => $this->lce_id,
            'lce_lead_id' => $this->lce_lead_id,
            'lce_status_id' => $this->lce_status_id,
            'lce_agent_user_id' => $this->lce_agent_user_id,
            'lce_expert_user_id' => $this->lce_expert_user_id,
            'lce_product_id' => $this->lce_product_id,
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
            'sort' => ['defaultOrder' => ['lce_id' => SORT_ASC]],
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

        $query->with(['lceAgentUser']);

        return $dataProvider;
    }

}
