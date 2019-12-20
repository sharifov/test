<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserProjectParams]].
 *
 * @see UserProjectParams
 */
class UserProjectParamsQuery extends \yii\db\ActiveQuery
{

    /**
     * @param string $phone
     * @return $this
     */
    public function byPhone(string $phone): self
    {
        return $this->where(['upp_tw_phone_number' => $phone])->orderBy(['upp_created_dt' => SORT_DESC]);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function byEmail(string $email): self
    {
        return $this->where(['upp_email' => $email])->orderBy(['upp_created_dt' => SORT_DESC]);
    }
}
