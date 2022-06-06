<?php

namespace modules\abac\src\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\abac\src\entities\AbacPolicy;

/**
 * AbacPolicySearch represents the model behind the search form of `modules\abac\src\entities\AbacPolicy`.
 */
class AbacPolicySearch extends AbacPolicy
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ap_id', 'ap_effect', 'ap_sort_order', 'ap_created_user_id', 'ap_updated_user_id'], 'integer'],
            [['ap_rule_type', 'ap_subject', 'ap_subject_json', 'ap_object', 'ap_action', 'ap_action_json', 'ap_title', 'ap_created_dt', 'ap_updated_dt', 'ap_enabled'], 'safe'],
            [['ap_hash_code'], 'string'],
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
        $query = AbacPolicy::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ap_id' => SORT_DESC]],
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
            'ap_id' => $this->ap_id,
            'ap_effect' => $this->ap_effect,
            'ap_sort_order' => $this->ap_sort_order,
            'DATE(ap_created_dt)' => $this->ap_created_dt,
            'DATE(ap_updated_dt)' => $this->ap_updated_dt,
            'ap_created_user_id' => $this->ap_created_user_id,
            'ap_updated_user_id' => $this->ap_updated_user_id,
            'ap_enabled' => $this->ap_enabled,
        ]);

        $query->andFilterWhere(['like', 'ap_rule_type', $this->ap_rule_type])
            ->andFilterWhere(['like', 'ap_subject', $this->ap_subject])
            ->andFilterWhere(['like', 'ap_subject_json', $this->ap_subject_json])
            ->andFilterWhere(['like', 'ap_object', $this->ap_object])
            ->andFilterWhere(['like', 'ap_action', $this->ap_action])
            ->andFilterWhere(['like', 'ap_action_json', $this->ap_action_json])
            ->andFilterWhere(['like', 'ap_hash_code', $this->ap_hash_code])
            ->andFilterWhere(['like', 'ap_title', $this->ap_title]);

        return $dataProvider;
    }
}
