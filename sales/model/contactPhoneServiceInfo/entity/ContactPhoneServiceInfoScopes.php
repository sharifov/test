<?php

namespace sales\model\contactPhoneServiceInfo\entity;

/**
* @see ContactPhoneServiceInfo
*/
class ContactPhoneServiceInfoScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ContactPhoneServiceInfo[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ContactPhoneServiceInfo|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
