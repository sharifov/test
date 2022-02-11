<?php

namespace src\model\phoneNumberRedial\entity\Scopes;

/**
* @see \src\model\phoneNumberRedial\entity\PhoneNumberRedial
*/
class PhoneNumberRedialQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \src\model\phoneNumberRedial\entity\PhoneNumberRedial[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \src\model\phoneNumberRedial\entity\PhoneNumberRedial|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
