<?php

namespace modules\fileStorage\src\entity\fileUser;

/**
* @see FileUser
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileUser[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileUser|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
