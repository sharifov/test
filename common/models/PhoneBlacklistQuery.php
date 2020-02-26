<?php

namespace common\models;

/**
 * Class PhoneBlacklistQuery
 */
class PhoneBlacklistQuery extends \yii\db\ActiveQuery
{
    public $specialChars = ['*', '.'];

    /**
     * @return PhoneBlacklistQuery
     */
    public function active(): PhoneBlacklistQuery
    {
        return $this->andWhere(['pbl_enabled' => true]);
    }

    /**
     * @return PhoneBlacklistQuery
     */
    public function activeByExpired(): PhoneBlacklistQuery
    {
        //return $this->andWhere(['OR', ['IS', 'pbl_expiration_date', null], ['>=', 'pbl_expiration_date', date('Y-m-d')]]);
        //return $this->andWhere(['AND', ['IS NOT', 'pbl_expiration_date', null], ['>=', 'pbl_expiration_date', date('Y-m-d')]]);
            //->orWhere(['IS', 'pbl_expiration_date', null]);
        return $this->andWhere([
            'OR',
            ['IS', 'pbl_expiration_date', NULL],
            ['>=', 'pbl_expiration_date', date('Y-m-d')]
        ]);
       /*
        pbl_expiration_date IS NULL
        OR
        pbl_expiration_date >= '2020-02-26'
         */
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function isExists(string $phone): bool
    {
        $query = $this->select(['pbl_phone'])
            ->where(['pbl_phone' => $phone])
            ->active()
            ->activeByExpired();

        if ($this->specialCharInPhone($phone)) {

        } else {

        }

        return $query->exists();
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function specialCharInPhone(string $phone): bool
    {
        foreach ($this->specialChars as $char) {
            if (strpos($phone, $char) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function isExists_orig(string $phone): bool
    {
        $list = $this->select(['pbl_phone'])->active()->activeByExpired()->column();
        return in_array($phone, $list, true);
    }
}
