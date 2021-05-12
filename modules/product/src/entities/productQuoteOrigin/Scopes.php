<?php

namespace modules\product\src\entities\productQuoteOrigin;

/**
* @see ProductQuoteOrigin
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProductQuoteOrigin[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProductQuoteOrigin|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
