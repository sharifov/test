<?php

namespace common\models\query;

use common\models\Client;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
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

	public function joinWithCcVisitor(string $visitorId): self
	{
		return $this->join('INNER JOIN', ClientChatVisitor::tableName(), 'ccv_client_id = id')
			->join('INNER JOIN', ClientChatVisitorData::tableName(), 'cvd_visitor_rc_id = :visitorId and ccv_cvd_id = cvd_id', ['visitorId' => $visitorId]);
	}
}
