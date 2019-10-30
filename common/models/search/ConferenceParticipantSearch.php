<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ConferenceParticipant;

/**
 * ConferenceParticipantSearch represents the model behind the search form of `common\models\ConferenceParticipant`.
 */
class ConferenceParticipantSearch extends ConferenceParticipant
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cp_id', 'cp_cf_id', 'cp_call_id', 'cp_status_id'], 'integer'],
            [['cp_call_sid', 'cp_join_dt', 'cp_leave_dt'], 'safe'],
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
        $query = ConferenceParticipant::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cp_id' => SORT_DESC]],
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
            'cp_id' => $this->cp_id,
            'cp_cf_id' => $this->cp_cf_id,
            'cp_call_id' => $this->cp_call_id,
            'cp_status_id' => $this->cp_status_id,
            'cp_join_dt' => $this->cp_join_dt,
            'cp_leave_dt' => $this->cp_leave_dt,
        ]);

        $query->andFilterWhere(['like', 'cp_call_sid', $this->cp_call_sid]);

        return $dataProvider;
    }
}
