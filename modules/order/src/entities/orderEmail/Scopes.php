<?php

namespace modules\order\src\entities\orderEmail;

/**
* @see OrderEmail
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return OrderEmail[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderEmail|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
