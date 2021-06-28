<?php

namespace sales\model\clientChat\componentRule\entity;

/**
* @see ClientChatComponentRule
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientChatComponentRule[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientChatComponentRule|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byValue(string $value): self
    {
        return $this->andWhere(['cccr_value' => $value]);
    }

    public function byComponentEventId(int $id): self
    {
        return $this->andWhere(['cccr_component_event_id' => $id]);
    }

    public function enabled(): self
    {
        return $this->andWhere(['cccr_enabled' => true]);
    }
}
