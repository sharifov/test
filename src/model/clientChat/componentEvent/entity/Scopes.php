<?php

namespace src\model\clientChat\componentEvent\entity;

use yii\db\Expression;

/**
* @see ClientChatComponentEvent
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientChatComponentEvent[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientChatComponentEvent|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byChannelId(int $id): self
    {
        return $this->andWhere(['ccce_chat_channel_id' => $id]);
    }

    public function orChannelIdIsNotSet()
    {
        return $this->orWhere(['is', 'ccce_chat_channel_id', new Expression('null')]);
    }

    public function enabled(): self
    {
        return $this->andWhere(['ccce_enabled' => true]);
    }

    public function beforeChatCreation(): self
    {
        return $this->andWhere(['ccce_event_type' => ClientChatComponentEvent::COMPONENT_EVENT_TYPE_BEFORE_CHAT_CREATION]);
    }

    public function afterChatCreation(): self
    {
        return $this->andWhere(['ccce_event_type' => ClientChatComponentEvent::COMPONENT_EVENT_TYPE_AFTER_CHAT_CREATION]);
    }
}
