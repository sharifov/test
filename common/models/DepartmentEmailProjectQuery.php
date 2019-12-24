<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * Class DepartmentEmailProjectQuery
 */
class DepartmentEmailProjectQuery extends ActiveQuery
{
    /**
     * @param string $email
     * @return $this
     */
    public function byEmail(string $email): self
    {
        return $this->where(['dep_email' => $email, 'dep_enable' => true])->limit(1);
    }
}
