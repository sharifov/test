<?php

namespace sales\model\clientChat\entity\search;

use sales\model\clientChat\entity\ClientChat;
use yii\data\ActiveDataProvider;

class ClientChatClientChatsSearch extends ClientChat
{
    public function searchChats(int $clientId): ActiveDataProvider
    {
        $query = static::find()->with(['cchProject', 'cchDep', 'cchChannel', 'cchOwnerUser', 'lastMessage']);
        $query->byClientId($clientId)->orderBy(['cch_id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        return $dataProvider;
    }
}
