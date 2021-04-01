<?php

namespace modules\order\src\processManager;

use yii\db\ActiveQuery;

/**
 * Class Scopes
 *
 * @see OrderProcessManager
 */
class Scopes extends ActiveQuery
{
    public function byId(int $id): self
    {
        return $this->andWhere(['opm_id' => $id]);
    }

    public function clickToBook(): self
    {
        return $this->andWhere(['opm_type' => Type::CLICK_TO_BOOK]);
    }

    public function phoneToBook(): self
    {
        return $this->andWhere(['opm_type' => Type::PHONE_TO_BOOK]);
    }

    public function notStopped(): self
    {
        return $this->andWhere(['IS NOT', 'opm_status', Status::STOPPED]);
    }

    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }

    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }
}
