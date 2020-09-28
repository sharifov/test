<?php

namespace sales\model\clientChatFeedback\entity;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ClientChatFeedback]].
 *
 * @see ClientChatNote
 */
class clientChatFeedbackScopes extends ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return ClientChatFeedback[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientChatFeedback|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
