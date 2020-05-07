<?php

namespace common\models\query;

use common\models\Client;
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
        return $this->andWhere(['cl_id' => $id]);
    }
}
