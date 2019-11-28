<?php

namespace common\models\query;

use common\models\UserProjectParams;

/**
 * This is the ActiveQuery class for [[UserProjectParams]].
 *
 * @see UserProjectParams
 */
class UserProjectParamsQuery extends \yii\db\ActiveQuery
{

    /**
     * @param string $phone
     * @return UserProjectParams|null
     */
    public function findByPhone(string $phone):? UserProjectParams
    {
        return $this->where(['upp_tw_phone_number' => $phone])->orderBy(['upp_created_dt' => SORT_DESC])->one();
    }
}
