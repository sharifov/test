<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VisitorLog;

/**
 * VisitorLogSearch represents the model behind the search form of `common\models\VisitorLog`.
 */
class VisitorLogSearch extends VisitorLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vl_id', 'vl_project_id', 'vl_user_id', 'vl_client_id', 'vl_lead_id'], 'integer'],
            [['vl_source_cid', 'vl_ga_client_id', 'vl_ga_user_id', 'vl_gclid', 'vl_dclid', 'vl_utm_source', 'vl_utm_medium', 'vl_utm_campaign', 'vl_utm_term', 'vl_utm_content', 'vl_referral_url', 'vl_location_url', 'vl_user_agent', 'vl_ip_address', 'vl_visit_dt', 'vl_created_dt'], 'safe'],
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
        $query = VisitorLog::find();

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
            'vl_id' => $this->vl_id,
            'vl_project_id' => $this->vl_project_id,
            'vl_user_id' => $this->vl_user_id,
            'vl_client_id' => $this->vl_client_id,
            'vl_lead_id' => $this->vl_lead_id,
            'vl_visit_dt' => $this->vl_visit_dt,
            'vl_created_dt' => $this->vl_created_dt,
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
