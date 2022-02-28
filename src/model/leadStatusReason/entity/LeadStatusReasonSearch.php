<?php

namespace src\model\leadStatusReason\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\leadStatusReason\entity\LeadStatusReason;

/**
 * LeadStatusReasonSearch represents the model behind the search form of `src\model\leadStatusReason\entity\LeadStatusReason`.
 */
class LeadStatusReasonSearch extends LeadStatusReason
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lsr_id', 'lsr_enabled', 'lsr_comment_required', 'lsr_created_user_id', 'lsr_updated_user_id'], 'integer'],
            [['lsr_key', 'lsr_name', 'lsr_description', 'lsr_params', 'lsr_created_dt', 'lsr_updated_dt'], 'safe'],
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
        $query = LeadStatusReason::find();

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
            'lsr_id' => $this->lsr_id,
            'lsr_enabled' => $this->lsr_enabled,
            'lsr_comment_required' => $this->lsr_comment_required,
            'lsr_created_user_id' => $this->lsr_created_user_id,
            'lsr_updated_user_id' => $this->lsr_updated_user_id,
            'lsr_created_dt' => $this->lsr_created_dt,
            'lsr_updated_dt' => $this->lsr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'lsr_key', $this->lsr_key])
            ->andFilterWhere(['like', 'lsr_name', $this->lsr_name])
            ->andFilterWhere(['like', 'lsr_description', $this->lsr_description])
            ->andFilterWhere(['like', 'lsr_params', $this->lsr_params]);

        return $dataProvider;
    }
}
