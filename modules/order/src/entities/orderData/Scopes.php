<?php

namespace modules\order\src\entities\orderData;

/**
* @see OrderData
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return OrderData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
