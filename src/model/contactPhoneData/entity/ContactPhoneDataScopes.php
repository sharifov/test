<?php

namespace src\model\contactPhoneData\entity;

/**
* @see ContactPhoneData
*/
class ContactPhoneDataScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ContactPhoneData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ContactPhoneData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
