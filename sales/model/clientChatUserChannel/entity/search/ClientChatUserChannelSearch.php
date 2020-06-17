<?php

namespace sales\model\clientChatUserChannel\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;

class ClientChatUserChannelSearch extends ClientChatUserChannel
{
    public function rules(): array
    {
        return [
            ['ccuc_channel_id', 'integer'],

            ['ccuc_created_dt', 'safe'],

            ['ccuc_created_user_id', 'integer'],

            ['ccuc_user_id', 'integer'],
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
            'ccuc_user_id' => $this->ccuc_user_id,
            'ccuc_channel_id' => $this->ccuc_channel_id,
            'ccuc_created_dt' => $this->ccuc_created_dt,
            'ccuc_created_user_id' => $this->ccuc_created_user_id,
        ]);

        return $dataProvider;
    }
}
