<?php

namespace src\model\phoneNumberRedial\entity\Scopes;

use src\model\phoneNumberRedial\entity\PhoneNumberRedial;
use yii\db\Expression;

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

    public function enabled()
    {
        return $this->andWhere(['pnr_enabled' => 1]);
    }

    /**
     * @param string $phone
     * @return PhoneNumberRedial|null
     */
    public static function getOneMatchingByClientPhone(string $phone): ?PhoneNumberRedial
    {
        return PhoneNumberRedial::find()
            ->where(new Expression(":phone REGEXP concat('^', TRIM(pnr_phone_pattern), '$') = 1", [
                'phone' => $phone
            ]))
            ->enabled()
            ->orderBy('rand()')
            ->limit(1)
            ->one();
    }
}
