<?php

namespace src\model\leadBusinessExtraQueueLog\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;

/**
 * LeadBusinessExtraQueueLogSearch represents the model behind the search form of `src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog`.
 */
class LeadBusinessExtraQueueLogSearch extends LeadBusinessExtraQueueLog
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['lbeql_id', 'lbeql_lbeqr_id', 'lbeql_lead_id', 'lbeql_status', 'lbeql_lead_owner_id'], 'integer'],
            [['lbeql_created_dt', 'lbeql_updated_dt'], 'safe'],
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
        $query = LeadBusinessExtraQueueLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lbeql_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lbeql_id' => $this->lbeql_id,
            'lbeql_lbeqr_id' => $this->lbeql_lbeqr_id,
            'lbeql_lead_id' => $this->lbeql_lead_id,
            'lbeql_status' => $this->lbeql_status,
            'lbeql_lead_owner_id' => $this->lbeql_lead_owner_id,
            'lbeql_created_dt' => $this->lbeql_created_dt,
            'lbeql_updated_dt' => $this->lbeql_updated_dt,
        ]);

        return $dataProvider;
    }
}
