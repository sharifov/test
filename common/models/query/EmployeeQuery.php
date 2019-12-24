<?php


namespace common\models\query;

use common\models\Employee;

/**
 * Class EmployeeQuery
 */
class EmployeeQuery extends \yii\db\ActiveQuery
{

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['status' => Employee::STATUS_ACTIVE]);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function byEmail(string $email): self
    {
        return $this->andWhere(['email' => $email])->limit(1);
    }
}
