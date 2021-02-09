<?php

namespace modules\fileStorage\src\entity\fileShare;

/**
* @see FileShare
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileShare[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileShare|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
