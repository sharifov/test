<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ConferenceParticipant]].
 *
 * @see ConferenceParticipant
 */
class ConferenceParticipantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ConferenceParticipant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceParticipant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
