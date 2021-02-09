<?php

namespace sales\model\shiftSchedule\entity\shift;

/**
* @see Shift
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return Shift[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return Shift|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
