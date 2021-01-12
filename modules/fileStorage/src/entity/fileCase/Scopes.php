<?php

namespace modules\fileStorage\src\entity\fileCase;

/**
* @see FileCase
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileCase[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileCase|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
