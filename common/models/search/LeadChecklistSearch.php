<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadChecklist;

/**
 * LeadChecklistSearch represents the model behind the search form of `common\models\LeadChecklist`.
 */
class LeadChecklistSearch extends LeadChecklist
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lc_type_id', 'lc_lead_id', 'lc_user_id'], 'integer'],
            [['lc_notes', 'lc_created_dt'], 'safe'],
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
        $query = LeadChecklist::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lc_created_dt' => SORT_DESC]],
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
            'lc_type_id' => $this->lc_type_id,
            'lc_lead_id' => $this->lc_lead_id,
            'lc_user_id' => $this->lc_user_id,
            'lc_created_dt' => $this->lc_created_dt,
        ]);

        $query->andFilterWhere(['like', 'lc_notes', $this->lc_notes]);

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
        $query = LeadChecklist::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lc_created_dt' => SORT_ASC]],
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
            'lc_type_id' => $this->lc_type_id,
            'lc_lead_id' => $this->lc_lead_id,
            'lc_user_id' => $this->lc_user_id,
            'lc_created_dt' => $this->lc_created_dt,
        ]);

        $query->andFilterWhere(['like', 'lc_notes', $this->lc_notes]);

        $query->with(['lcType', 'lcLead', 'lcUser']);

        return $dataProvider;
    }



}
