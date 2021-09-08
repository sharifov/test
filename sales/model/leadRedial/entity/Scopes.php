<?php

namespace sales\model\leadRedial\entity;

/**
* @see CallRedialUserAccess
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return CallRedialUserAccess[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CallRedialUserAccess|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
