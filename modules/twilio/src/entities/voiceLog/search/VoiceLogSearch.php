<?php

namespace modules\twilio\src\entities\voiceLog\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\twilio\src\entities\voiceLog\VoiceLog;

/**
 * VoiceLogSearch represents the model behind the search form of `modules\twilio\src\entities\voiceLog\VoiceLog`.
 */
class VoiceLogSearch extends VoiceLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vl_id'], 'integer'],
            [['vl_call_sid', 'vl_account_sid', 'vl_from', 'vl_to', 'vl_call_status', 'vl_api_version', 'vl_direction', 'vl_forwarded_from', 'vl_caller_name', 'vl_parent_call_sid', 'vl_call_duration', 'vl_sip_response_code', 'vl_recording_url', 'vl_recording_sid', 'vl_recording_duration', 'vl_timestamp', 'vl_callback_source', 'vl_sequence_number', 'vl_created_dt'], 'safe'],
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
        $query = VoiceLog::find();

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
            'vl_created_dt' => $this->vl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'vl_call_sid', $this->vl_call_sid])
            ->andFilterWhere(['like', 'vl_account_sid', $this->vl_account_sid])
            ->andFilterWhere(['like', 'vl_from', $this->vl_from])
            ->andFilterWhere(['like', 'vl_to', $this->vl_to])
            ->andFilterWhere(['like', 'vl_call_status', $this->vl_call_status])
            ->andFilterWhere(['like', 'vl_api_version', $this->vl_api_version])
            ->andFilterWhere(['like', 'vl_direction', $this->vl_direction])
            ->andFilterWhere(['like', 'vl_forwarded_from', $this->vl_forwarded_from])
            ->andFilterWhere(['like', 'vl_caller_name', $this->vl_caller_name])
            ->andFilterWhere(['like', 'vl_parent_call_sid', $this->vl_parent_call_sid])
            ->andFilterWhere(['like', 'vl_call_duration', $this->vl_call_duration])
            ->andFilterWhere(['like', 'vl_sip_response_code', $this->vl_sip_response_code])
            ->andFilterWhere(['like', 'vl_recording_url', $this->vl_recording_url])
            ->andFilterWhere(['like', 'vl_recording_sid', $this->vl_recording_sid])
            ->andFilterWhere(['like', 'vl_recording_duration', $this->vl_recording_duration])
            ->andFilterWhere(['like', 'vl_timestamp', $this->vl_timestamp])
            ->andFilterWhere(['like', 'vl_callback_source', $this->vl_callback_source])
            ->andFilterWhere(['like', 'vl_sequence_number', $this->vl_sequence_number]);

        return $dataProvider;
    }
}
