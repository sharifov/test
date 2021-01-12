<?php

namespace modules\fileStorage\src\entity\fileLog;

/**
* @see FileLog
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileLog[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileLog|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
