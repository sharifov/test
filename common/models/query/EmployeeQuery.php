<?php


namespace common\models\query;

use common\models\Employee;
use common\models\UserGroup;
use yii\db\Expression;

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

//    public function supervisorsByGroups(array $groups)
//	{
//		return $this->leftJoin('auth_assignment','auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => Employee::SUPE])->innerJoin(UserGroup::tableName(), new Expression(''))
//	}
}
