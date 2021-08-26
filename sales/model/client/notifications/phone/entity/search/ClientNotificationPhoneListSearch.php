<?php

namespace sales\model\client\notifications\phone\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;

class ClientNotificationPhoneListSearch extends ClientNotificationPhoneList
{
    public function rules(): array
    {
        return [
            ['cnfl_call_sid', 'safe'],

            ['cnfl_created_dt', 'safe'],

            ['cnfl_end', 'safe'],

            ['cnfl_file_url', 'safe'],

            ['cnfl_from_phone_id', 'integer'],

            ['cnfl_id', 'integer'],

            ['cnfl_message', 'safe'],

            ['cnfl_start', 'safe'],

            ['cnfl_status_id', 'integer'],

            ['cnfl_to_client_phone_id', 'integer'],

            ['cnfl_updated_dt', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cnfl_id' => $this->cnfl_id,
            'cnfl_status_id' => $this->cnfl_status_id,
            'cnfl_from_phone_id' => $this->cnfl_from_phone_id,
            'cnfl_to_client_phone_id' => $this->cnfl_to_client_phone_id,
            'cnfl_start' => $this->cnfl_start,
            'cnfl_end' => $this->cnfl_end,
            'cnfl_created_dt' => $this->cnfl_created_dt,
            'cnfl_updated_dt' => $this->cnfl_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cnfl_message', $this->cnfl_message])
            ->andFilterWhere(['like', 'cnfl_file_url', $this->cnfl_file_url])
            ->andFilterWhere(['like', 'cnfl_call_sid', $this->cnfl_call_sid]);

        return $dataProvider;
    }
}
