<?php

namespace modules\fileStorage\src\entity\fileLead;

/**
* @see FileLead
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileLead[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileLead|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
