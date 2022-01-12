<?php

namespace src\model\contactPhoneList\entity;

/**
* @see ContactPhoneList
*/
class ContactPhoneListScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ContactPhoneList[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ContactPhoneList|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
