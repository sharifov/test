<?php

namespace sales\model\clientChatVisitor\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;

class ClientChatVisitorSearch extends ClientChatVisitor
{
    public function rules(): array
    {
        return [
            ['ccv_cch_id', 'integer'],

            ['ccv_client_id', 'integer'],

            ['ccv_cvd_id', 'integer'],

            ['ccv_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'ccv_id' => SORT_DESC
				]
			]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccv_id' => $this->ccv_id,
            'ccv_cch_id' => $this->ccv_cch_id,
            'ccv_cvd_id' => $this->ccv_cvd_id,
            'ccv_client_id' => $this->ccv_client_id,
        ]);

        return $dataProvider;
    }
}
