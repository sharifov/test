<?php

namespace sales\model\callLog\entity\callLog;

/**
 *
 * @see CallLog
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * @param null $db
     * @return CallLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }

    /**
     * @param null $db
     * @return CallLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }
}
