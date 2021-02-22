<?php

namespace modules\cruise\src\entity\cruiseCabin;

/**
* @see CruiseCabin
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return CruiseCabin[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CruiseCabin|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
