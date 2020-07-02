<?php

namespace sales\model\clientChatData\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatData\entity\ClientChatData;

class ClientChatDataSearch extends ClientChatData
{
    public function rules(): array
    {
        return [
            ['ccd_cch_id', 'integer'],

            ['ccd_city', 'safe'],

            ['ccd_country', 'safe'],

            ['ccd_latitude', 'number'],

            ['ccd_local_time', 'safe'],

            ['ccd_longitude', 'number'],

            ['ccd_referrer', 'safe'],

            ['ccd_region', 'safe'],

            ['ccd_timezone', 'safe'],

            ['ccd_title', 'safe'],

            ['ccd_url', 'safe'],

			[['ccd_created_dt', 'ccd_updated_dt'], 'safe'],
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
            'ccd_cch_id' => $this->ccd_cch_id,
            'ccd_latitude' => $this->ccd_latitude,
            'ccd_longitude' => $this->ccd_longitude,
            'date_format(ccd_created_dt, "%Y-%m-%d")' => $this->ccd_created_dt,
            'date_format(ccd_updated_dt, "%Y-%m-%d")' => $this->ccd_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ccd_country', $this->ccd_country])
            ->andFilterWhere(['like', 'ccd_region', $this->ccd_region])
            ->andFilterWhere(['like', 'ccd_city', $this->ccd_city])
            ->andFilterWhere(['like', 'ccd_url', $this->ccd_url])
            ->andFilterWhere(['like', 'ccd_title', $this->ccd_title])
            ->andFilterWhere(['like', 'ccd_referrer', $this->ccd_referrer])
            ->andFilterWhere(['like', 'ccd_timezone', $this->ccd_timezone])
            ->andFilterWhere(['like', 'ccd_local_time', $this->ccd_local_time]);

        return $dataProvider;
    }
}
