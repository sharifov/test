<?php

namespace modules\fileStorage\src\entity\fileClient;

/**
* @see FileClient
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FileClient[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileClient|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
