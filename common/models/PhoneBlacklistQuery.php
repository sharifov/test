<?php

namespace common\models;

use Yii;

/**
 * Class PhoneBlacklistQuery
 */
class PhoneBlacklistQuery extends \yii\db\ActiveQuery
{
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
        return $this->andWhere([
            'OR',
            ['IS', 'pbl_expiration_date', null],
            ['>=', 'pbl_expiration_date', date('Y-m-d H:i:s')]
        ]);
    }

    public function byPhone(string $phone): self
    {
        return $this->andWhere(['pbl_phone' => $phone]);
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function isExists(string $phone): bool
    {
        $query = $this->select(['pbl_phone']);
        //->where(['pbl_phone' => $phone]);

        // $sql = "REGEXP_LIKE(:phone, CONCAT('^', REPLACE(REPLACE(REPLACE(pbl_phone, '.', '[0-9]'), '*', '[0-9]*'), '+', '\\\\+'), '$')) = 1"; // TODO Mysql8
        $sql = ":phone REGEXP CONCAT('^', REPLACE(REPLACE(REPLACE(pbl_phone, '.', '[0-9]'), '*', '[0-9]*'), '+', '\\\\+'), '$') = 1";


        $query->where($sql, [':phone' => $phone]);
        $query->active()->activeByExpired();

        return $query->exists();
    }

    public function getCountOfRowsByPhone(string $phone)
    {
        $query = PhoneBlacklist::find();
        $query->byPhone($phone);
        $query->active();
    }
}
