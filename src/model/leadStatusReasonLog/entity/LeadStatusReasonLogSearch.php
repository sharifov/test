<?php

namespace src\model\leadStatusReasonLog\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;

/**
 * LeadStatusReasonLogSearch represents the model behind the search form of `src\model\leadStatusReasonLog\entity\LeadStatusReasonLog`.
 */
class LeadStatusReasonLogSearch extends LeadStatusReasonLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lsrl_id', 'lsrl_lead_flow_id', 'lsrl_lead_status_reason_id'], 'integer'],
            [['lsrl_comment', 'lsrl_created_dt'], 'safe'],
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
        $query = LeadStatusReasonLog::find();

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
            'lsrl_id' => $this->lsrl_id,
            'lsrl_lead_flow_id' => $this->lsrl_lead_flow_id,
            'lsrl_lead_status_reason_id' => $this->lsrl_lead_status_reason_id,
            'lsrl_created_dt' => $this->lsrl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'lsrl_comment', $this->lsrl_comment]);

        return $dataProvider;
    }
}
