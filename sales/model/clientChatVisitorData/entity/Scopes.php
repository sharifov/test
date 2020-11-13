<?php

namespace sales\model\clientChatVisitorData\entity;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use yii\db\ActiveQuery;

/**
 * @see ClientChatVisitorData
 */
class Scopes extends ActiveQuery
{
    public function byVisitorId(string $id): ActiveQuery
    {
        return $this->andWhere(['cvd_visitor_rc_id' => $id]);
    }

    public function joinWithChat(int $id): self
    {
        return $this->innerJoin(ClientChatVisitor::tableName(), 'ccv_cvd_id = cvd_id and ccv_cch_id = :chatId', ['chatId' => $id]);
    }
}
