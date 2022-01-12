<?php

namespace src\model\clientChatRequest\entity\search;

use yii\data\ActiveDataProvider;
use src\model\clientChatRequest\entity\ClientChatRequest;

class ClientChatRequestSearch extends ClientChatRequest
{
    public function rules(): array
    {
        return [
            ['ccr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ccr_event', 'safe'],

            ['ccr_id', 'integer'],

            ['ccr_json_data', 'safe'],

            [['ccr_rid'], 'string', 'max' => 150],
            [['ccr_visitor_id'], 'string', 'max' => 100],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find()->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ccr_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccr_id' => $this->ccr_id,
            'ccr_rid' => $this->ccr_rid,
            'ccr_visitor_id' => $this->ccr_visitor_id,
            'DATE(ccr_created_dt)' => $this->ccr_created_dt,
        ]);

        $query->andFilterWhere(['ccr_event' => $this->ccr_event]);

        $query->andFilterWhere(['like', 'ccr_json_data', $this->ccr_json_data]);

        return $dataProvider;
    }
}
