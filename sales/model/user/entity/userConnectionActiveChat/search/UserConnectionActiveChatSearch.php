<?php

namespace sales\model\user\entity\userConnectionActiveChat\search;

use yii\data\ActiveDataProvider;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;

class UserConnectionActiveChatSearch extends UserConnectionActiveChat
{
    public function rules(): array
    {
        return [
            ['ucac_chat_id', 'integer'],

            ['ucac_conn_id', 'integer'],
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
            'ucac_conn_id' => $this->ucac_conn_id,
            'ucac_chat_id' => $this->ucac_chat_id,
        ]);

        return $dataProvider;
    }
}
