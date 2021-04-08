<?php

namespace modules\order\src\entities\orderContact;

/**
* @see OrderContact
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return OrderContact[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return OrderContact|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byOrderId(int $id): self
    {
        return $this->andWhere(['oc_order_id' => $id]);
    }

    public function last(): self
    {
        return $this->orderBy(['oc_id' => SORT_DESC]);
    }
}
