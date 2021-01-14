<?php

namespace modules\fileStorage\src\entity\fileStorage;

/**
 * @see FileSystem
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * @param null $db
     * @return FileStorage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return FileStorage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
