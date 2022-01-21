<?php

namespace src\model\leadRequest\entity;

/**
 * This is the ActiveQuery class for [[LeadRequest]].
 *
 * @see LeadRequest
 */
class LeadRequestScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return LeadRequest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadRequest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
