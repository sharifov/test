<?php

namespace modules\twilio\src\entities\conferenceLog;

/**
 * This is the ActiveQuery class for [[ConferenceLog]].
 *
 * @see ConferenceLog
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ConferenceLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
