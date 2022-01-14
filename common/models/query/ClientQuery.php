<?php

namespace common\models\query;

use common\models\Client;
use src\model\clientChatVisitor\entity\ClientChatVisitor;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use src\model\clientVisitor\entity\ClientVisitor;
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

    public function byVisitor(string $visitorId): self
    {
        return $this->innerJoin(
            ClientVisitor::tableName(),
            'cv_client_id = id AND cv_visitor_id = :visitorId',
            ['visitorId' => $visitorId]
        );
    }

    public function byProject(int $projectId): ClientQuery
    {
        return $this->andWhere(['cl_project_id' => $projectId]);
    }
}
