<?php

namespace src\model\leadStatusReason\entity;

/**
 * This is the ActiveQuery class for [[LeadStatusReason]].
 *
 * @see LeadStatusReason
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadStatusReason[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadStatusReason|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byKey(string $key): Scopes
    {
        return $this->andWhere(['lsr_key' => $key]);
    }

    public function enabled(): Scopes
    {
        return $this->andWhere(['lsr_enabled' => true]);
    }
}
