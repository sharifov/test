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
     * @param string $phone
     * @return bool
     */
    public function isExists(string $phone): bool
    {
        $list = $this->select(['pbl_phone'])->active()->column();
        return in_array($phone, $list, true);
    }
}
