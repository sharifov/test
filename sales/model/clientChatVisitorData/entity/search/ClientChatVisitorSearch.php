<?php

namespace sales\model\clientChatVisitorData\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;

class ClientChatVisitorSearch extends ClientChatVisitorData
{
    public function rules(): array
    {
        return [
            ['cvd_city', 'safe'],

            ['cvd_country', 'safe'],

            ['cvd_created_dt', 'safe'],

            ['cvd_data', 'safe'],

            ['cvd_id', 'integer'],

            ['cvd_latitude', 'number'],

            ['cvd_local_time', 'safe'],

            ['cvd_longitude', 'number'],

            ['cvd_referrer', 'safe'],

            ['cvd_region', 'safe'],

            ['cvd_timezone', 'safe'],

            ['cvd_title', 'safe'],

            ['cvd_updated_dt', 'safe'],

            ['cvd_url', 'safe'],

            ['cvd_visitor_rc_id', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cvd_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cvd_id' => $this->cvd_id,
            'cvd_latitude' => $this->cvd_latitude,
            'cvd_longitude' => $this->cvd_longitude,
            'cvd_created_dt' => $this->cvd_created_dt,
            'cvd_updated_dt' => $this->cvd_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cvd_country', $this->cvd_country])
            ->andFilterWhere(['like', 'cvd_region', $this->cvd_region])
            ->andFilterWhere(['like', 'cvd_city', $this->cvd_city])
            ->andFilterWhere(['like', 'cvd_url', $this->cvd_url])
            ->andFilterWhere(['like', 'cvd_title', $this->cvd_title])
            ->andFilterWhere(['like', 'cvd_referrer', $this->cvd_referrer])
            ->andFilterWhere(['like', 'cvd_timezone', $this->cvd_timezone])
            ->andFilterWhere(['like', 'cvd_local_time', $this->cvd_local_time])
            ->andFilterWhere(['like', 'cvd_data', $this->cvd_data])
            ->andFilterWhere(['like', 'cvd_visitor_rc_id', $this->cvd_visitor_rc_id]);

        return $dataProvider;
    }
}
