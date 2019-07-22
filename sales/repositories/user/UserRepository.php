<?php

namespace sales\repositories\user;

use common\models\Employee;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class UserRepository
 * @method null|Employee get($id)
 */
class UserRepository extends Repository
{
    /**
     * @param $id
     * @return Employee
     */
    public function find($id): Employee
    {
        if ($user = Employee::findOne($id)) {
            return $user;
        }
        throw new NotFoundException('User is not found');
    }

}