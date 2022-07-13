<?php

namespace src\model\leadBusinessExtraQueueRule\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;

/**
 * LeadBusinessExtraQueueRuleSearch represents the model behind the search form of `src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule`.
 */
class LeadBusinessExtraQueueRuleSearch extends LeadBusinessExtraQueueRule
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['lbeqr_id', 'lbeqr_updated_user_id'], 'integer'],
            [['lbeqr_key', 'lbeqr_name', 'lbeqr_description', 'lbeqr_params_json', 'lbeqr_created_dt', 'lbeqr_updated_dt'], 'safe'],
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
        $query = LeadBusinessExtraQueueRule::find();

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
            'lbeqr_id' => $this->lbeqr_id,
            'lbeqr_updated_user_id' => $this->lbeqr_updated_user_id,
            'lbeqr_created_dt' => $this->lbeqr_created_dt,
            'lbeqr_updated_dt' => $this->lbeqr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'lbeqr_key', $this->lbeqr_key])
              ->andFilterWhere(['like', 'lbeqr_name', $this->lbeqr_name])
              ->andFilterWhere(['like', 'lbeqr_description', $this->lbeqr_description])
              ->andFilterWhere(['like', 'lbeqr_params_json', $this->lbeqr_params_json]);

        return $dataProvider;
    }
}
