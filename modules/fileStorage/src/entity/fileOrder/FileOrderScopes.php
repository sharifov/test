<?php

namespace modules\fileStorage\src\entity\fileOrder;

/**
* @see FileOrder
*/
class FileOrderScopes extends \yii\db\ActiveQuery
{
    /**
     * @return FileOrder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @return FileOrder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
