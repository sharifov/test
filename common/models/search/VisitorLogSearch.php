<?php

namespace common\models\search;

use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use common\models\VisitorLog;

class VisitorLogSearch extends VisitorLog
{
    public function rules(): array
    {
        return [
            ['vl_project_id', 'integer'],
            ['vl_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['vl_project_id' => 'id']],

            ['vl_customer_id', 'integer'],

            ['vl_client_id', 'integer'],
            ['vl_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['vl_client_id' => 'id']],

            ['vl_lead_id', 'integer'],
            ['vl_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['vl_lead_id' => 'id']],

            ['vl_source_cid', 'string', 'max' => 10],

            ['vl_ga_client_id', 'string', 'max' => 36],

            ['vl_ga_user_id', 'string', 'max' => 36],

            ['vl_gclid', 'string', 'max' => 100],

            ['vl_dclid', 'string', 'max' => 255],

            ['vl_utm_source', 'string', 'max' => 50],

            ['vl_utm_medium', 'string', 'max' => 50],

            ['vl_utm_campaign', 'string', 'max' => 50],

            ['vl_utm_term', 'string', 'max' => 50],

            ['vl_utm_content', 'string', 'max' => 50],

            ['vl_referral_url', 'string', 'max' => 500],

            ['vl_location_url', 'string', 'max' => 500],

            ['vl_user_agent', 'string', 'max' => 500],

            ['vl_ip_address', 'string', 'max' => 39],

            ['vl_visit_dt', 'date', 'format' => 'php:Y-m-d'],

            ['vl_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = VisitorLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['vl_id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->vl_visit_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'vl_visit_dt', $this->vl_visit_dt, $user->timezone);
        }

        if ($this->vl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'vl_created_dt', $this->vl_created_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'vl_id' => $this->vl_id,
            'vl_project_id' => $this->vl_project_id,
            'vl_customer_id' => $this->vl_customer_id,
            'vl_client_id' => $this->vl_client_id,
            'vl_lead_id' => $this->vl_lead_id,
        ]);

        $query->andFilterWhere(['like', 'vl_source_cid', $this->vl_source_cid])
            ->andFilterWhere(['like', 'vl_ga_client_id', $this->vl_ga_client_id])
            ->andFilterWhere(['like', 'vl_ga_user_id', $this->vl_ga_user_id])
            ->andFilterWhere(['like', 'vl_gclid', $this->vl_gclid])
            ->andFilterWhere(['like', 'vl_dclid', $this->vl_dclid])
            ->andFilterWhere(['like', 'vl_utm_source', $this->vl_utm_source])
            ->andFilterWhere(['like', 'vl_utm_medium', $this->vl_utm_medium])
            ->andFilterWhere(['like', 'vl_utm_campaign', $this->vl_utm_campaign])
            ->andFilterWhere(['like', 'vl_utm_term', $this->vl_utm_term])
            ->andFilterWhere(['like', 'vl_utm_content', $this->vl_utm_content])
            ->andFilterWhere(['like', 'vl_referral_url', $this->vl_referral_url])
            ->andFilterWhere(['like', 'vl_location_url', $this->vl_location_url])
            ->andFilterWhere(['like', 'vl_user_agent', $this->vl_user_agent])
            ->andFilterWhere(['like', 'vl_ip_address', $this->vl_ip_address]);

        return $dataProvider;
    }
}
