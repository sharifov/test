<?php

namespace modules\order\src\entities\orderRequest;

/**
* @see OrderRequest
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return OrderRequest[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderRequest|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
