<?php

namespace modules\twilio\src\entities\twilioJwtToken;

/**
 * This is the ActiveQuery class for [[TwilioJwtToken]].
 *
 * @see TwilioJwtToken
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return TwilioJwtToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TwilioJwtToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
