<?php

namespace common\models\query;

use common\models\Client;
use sales\model\ClientChatVisitor\entity\ClientChatVisitor;
use sales\model\ClientChatVisitorData\entity\ClientChatVisitorData;
use yii\db\ActiveQuery;

/**
 * Class ClientQuery
 */
class ClientQuery extends ActiveQuery
{
    public function byContact(): self
    {
        return $this->andWhere(['cl_type_id' => Client::TYPE_CONTACT]);
    }

    public function byId(?int $id): self
    {
        return $this->andWhere(['id' => $id]);
    }

    public function byUuid(string $uuid): self
	{
		return $this->andWhere(['uuid' => $uuid]);
	}

	public function joinWithCcVisitor(): self
	{
		return $this->join('INNER JOIN', ClientChatVisitor::tableName(), 'ccv_client_id = id');
	}

	public function joinWithCcVisitorData(string $visitorId): self
	{
		return $this->join('INNER JOIN', ClientChatVisitorData::tableName(), 'cvd_visitor_rc_id = :visitorId', ['visitorId' => $visitorId]);
	}
}
