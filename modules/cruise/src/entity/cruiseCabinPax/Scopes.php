<?php

namespace modules\cruise\src\entity\cruiseCabinPax;

/**
* @see CruiseCabinPax
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return CruiseCabinPax[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CruiseCabinPax|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
