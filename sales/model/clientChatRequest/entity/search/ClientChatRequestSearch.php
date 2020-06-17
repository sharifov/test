<?php

namespace sales\model\clientChatRequest\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatRequest\entity\ClientChatRequest;

class ClientChatRequestSearch extends ClientChatRequest
{
    public function rules(): array
    {
        return [
            ['ccr_created_dt', 'safe'],

            ['ccr_event', 'safe'],

            ['ccr_id', 'integer'],

            ['ccr_json_data', 'safe'],
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
            'ccr_id' => $this->ccr_id,
            'date_format(ccr_created_dt, "%y-%m-%d")' => $this->ccr_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ccr_event', $this->ccr_event]);
//            ->andFilterWhere(['like', 'ccr_json_data', $this->ccr_json_data]);

        return $dataProvider;
    }
}
