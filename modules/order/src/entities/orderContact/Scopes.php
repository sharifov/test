<?php

namespace modules\order\src\entities\orderContact;

/**
* @see OrderContact
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return OrderContact[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderContact|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
