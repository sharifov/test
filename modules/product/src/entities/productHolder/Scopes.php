<?php

namespace modules\product\src\entities\productHolder;

/**
* @see ProductHolder
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProductHolder[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProductHolder|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
