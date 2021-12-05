<?php

namespace sales\model\voip\phoneDevice\device;

/**
* @see PhoneDevice
*/
class PhoneDeviceScopes extends \yii\db\ActiveQuery
{
    public function byHash(string $hash): self
    {
        return $this->andWhere(['pd_hash' => $hash]);
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
