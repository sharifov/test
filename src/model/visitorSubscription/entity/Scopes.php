<?php

namespace src\model\visitorSubscription\entity;

/**
* @see VisitorSubscription
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return VisitorSubscription[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return VisitorSubscription|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byUid(string $id): self
    {
        return $this->andWhere(['vs_subscription_uid' => $id]);
    }

    public function enabled(): self
    {
        return $this->andWhere(['vs_enabled' => 1]);
    }

    public function byType(int $type): self
    {
        return $this->andWhere(['vs_type_id' => $type]);
    }
}
