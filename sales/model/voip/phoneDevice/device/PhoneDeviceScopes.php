<?php

namespace sales\model\voip\phoneDevice\device;

/**
* @see PhoneDevice
*/
class PhoneDeviceScopes extends \yii\db\ActiveQuery
{
    public function byId(int $id): self
    {
        return $this->andWhere(['pd_id' => $id]);
    }

    public function byUserId(int $userId): self
    {
        return $this->andWhere(['pd_user_id' => $userId]);
    }

    /**
    * @return PhoneDevice[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return PhoneDevice|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
