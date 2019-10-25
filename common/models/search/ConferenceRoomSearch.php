<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ConferenceRoom;

/**
 * ConferenceRoomSearch represents the model behind the search form of `common\models\ConferenceRoom`.
 */
class ConferenceRoomSearch extends ConferenceRoom
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cr_id', 'cr_enabled', 'cr_param_muted', 'cr_param_start_conference_on_enter', 'cr_param_end_conference_on_enter', 'cr_param_max_participants', 'cr_created_user_id', 'cr_updated_user_id'], 'integer'],
            [['cr_key', 'cr_name', 'cr_phone_number', 'cr_start_dt', 'cr_end_dt', 'cr_param_beep', 'cr_param_record', 'cr_param_region', 'cr_param_trim', 'cr_param_wait_url', 'cr_moderator_phone_number', 'cr_welcome_message', 'cr_created_dt', 'cr_updated_dt'], 'safe'],
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
        $query = ConferenceRoom::find();

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
            'cr_id' => $this->cr_id,
            'cr_enabled' => $this->cr_enabled,
            'cr_start_dt' => $this->cr_start_dt,
            'cr_end_dt' => $this->cr_end_dt,
            'cr_param_muted' => $this->cr_param_muted,
            'cr_param_start_conference_on_enter' => $this->cr_param_start_conference_on_enter,
            'cr_param_end_conference_on_enter' => $this->cr_param_end_conference_on_enter,
            'cr_param_max_participants' => $this->cr_param_max_participants,
            'cr_created_dt' => $this->cr_created_dt,
            'cr_updated_dt' => $this->cr_updated_dt,
            'cr_created_user_id' => $this->cr_created_user_id,
            'cr_updated_user_id' => $this->cr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'cr_key', $this->cr_key])
            ->andFilterWhere(['like', 'cr_name', $this->cr_name])
            ->andFilterWhere(['like', 'cr_phone_number', $this->cr_phone_number])
            ->andFilterWhere(['like', 'cr_param_beep', $this->cr_param_beep])
            ->andFilterWhere(['like', 'cr_param_record', $this->cr_param_record])
            ->andFilterWhere(['like', 'cr_param_region', $this->cr_param_region])
            ->andFilterWhere(['like', 'cr_param_trim', $this->cr_param_trim])
            ->andFilterWhere(['like', 'cr_param_wait_url', $this->cr_param_wait_url])
            ->andFilterWhere(['like', 'cr_moderator_phone_number', $this->cr_moderator_phone_number])
            ->andFilterWhere(['like', 'cr_welcome_message', $this->cr_welcome_message]);

        return $dataProvider;
    }
}
