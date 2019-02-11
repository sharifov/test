<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Call;

/**
 * CallSearch represents the model behind the search form of `common\models\Call`.
 */
class CallSearch extends Call
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['c_id', 'c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_is_new', 'c_is_deleted'], 'integer'],
            [['c_call_sid', 'c_account_sid', 'c_from', 'c_to', 'c_sip', 'c_call_status', 'c_api_version', 'c_direction', 'c_forwarded_from', 'c_caller_name', 'c_parent_call_sid', 'c_call_duration', 'c_sip_response_code', 'c_recording_url', 'c_recording_sid', 'c_recording_duration', 'c_timestamp', 'c_uri', 'c_sequence_number', 'c_created_dt', 'c_updated_dt', 'c_error_message', 'c_price'], 'safe'],
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
        $query = Call::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
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
            'c_id' => $this->c_id,
            'c_call_type_id' => $this->c_call_type_id,
            'c_lead_id' => $this->c_lead_id,
            'c_created_user_id' => $this->c_created_user_id,
            'c_created_dt' => $this->c_created_dt,
            'c_com_call_id' => $this->c_com_call_id,
            'c_updated_dt' => $this->c_updated_dt,
            'c_project_id' => $this->c_project_id,
            'c_is_new' => $this->c_is_new,
            'c_is_deleted' => $this->c_is_deleted,
            'c_price' => $this->c_price,
        ]);

        $query->andFilterWhere(['like', 'c_call_sid', $this->c_call_sid])
            ->andFilterWhere(['like', 'c_account_sid', $this->c_account_sid])
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            ->andFilterWhere(['like', 'c_sip', $this->c_sip])
            ->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_api_version', $this->c_api_version])
            ->andFilterWhere(['like', 'c_direction', $this->c_direction])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_parent_call_sid', $this->c_parent_call_sid])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_sip_response_code', $this->c_sip_response_code])
            ->andFilterWhere(['like', 'c_recording_url', $this->c_recording_url])
            ->andFilterWhere(['like', 'c_recording_sid', $this->c_recording_sid])
            ->andFilterWhere(['like', 'c_recording_duration', $this->c_recording_duration])
            ->andFilterWhere(['like', 'c_timestamp', $this->c_timestamp])
            ->andFilterWhere(['like', 'c_uri', $this->c_uri])
            ->andFilterWhere(['like', 'c_sequence_number', $this->c_sequence_number])
            ->andFilterWhere(['like', 'c_error_message', $this->c_error_message]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAgent($params)
    {
        $query = Call::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['c_id' => SORT_DESC]],
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
            'c_id' => $this->c_id,
            'c_call_type_id' => $this->c_call_type_id,
            'c_lead_id' => $this->c_lead_id,
            'c_created_user_id' => $this->c_created_user_id,
            'c_created_dt' => $this->c_created_dt,
            'c_com_call_id' => $this->c_com_call_id,
            'c_updated_dt' => $this->c_updated_dt,
            'c_project_id' => $this->c_project_id,
            'c_is_new' => $this->c_is_new,
            'c_is_deleted' => $this->c_is_deleted,
        ]);

        $query->andFilterWhere(['like', 'c_call_sid', $this->c_call_sid])
            ->andFilterWhere(['like', 'c_account_sid', $this->c_account_sid])
            ->andFilterWhere(['like', 'c_from', $this->c_from])
            ->andFilterWhere(['like', 'c_to', $this->c_to])
            ->andFilterWhere(['like', 'c_sip', $this->c_sip])
            ->andFilterWhere(['like', 'c_call_status', $this->c_call_status])
            ->andFilterWhere(['like', 'c_api_version', $this->c_api_version])
            ->andFilterWhere(['like', 'c_direction', $this->c_direction])
            ->andFilterWhere(['like', 'c_forwarded_from', $this->c_forwarded_from])
            ->andFilterWhere(['like', 'c_caller_name', $this->c_caller_name])
            ->andFilterWhere(['like', 'c_parent_call_sid', $this->c_parent_call_sid])
            ->andFilterWhere(['like', 'c_call_duration', $this->c_call_duration])
            ->andFilterWhere(['like', 'c_sip_response_code', $this->c_sip_response_code])
            ->andFilterWhere(['like', 'c_recording_url', $this->c_recording_url])
            ->andFilterWhere(['like', 'c_recording_sid', $this->c_recording_sid])
            ->andFilterWhere(['like', 'c_recording_duration', $this->c_recording_duration])
            ->andFilterWhere(['like', 'c_timestamp', $this->c_timestamp])
            ->andFilterWhere(['like', 'c_uri', $this->c_uri])
            ->andFilterWhere(['like', 'c_sequence_number', $this->c_sequence_number])
            ->andFilterWhere(['like', 'c_error_message', $this->c_error_message]);

        return $dataProvider;
    }
}
