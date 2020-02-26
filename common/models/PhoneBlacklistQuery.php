<?php

namespace common\models;

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
        return $this->andWhere(['OR', ['IS', 'pbl_expiration_date', null], ['>=', 'pbl_expiration_date', date('Y-m-d')]]);
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function isExists(string $phone): bool
    {
        $list = $this->select(['pbl_phone'])->active()->activeByExpired()->column();
        return in_array($phone, $list, true);
    }
}
