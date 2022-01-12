<?php

namespace src\repositories\user;

use common\models\Employee;
use src\repositories\NotFoundException;

/**
 * Class UserRepository
 */
class UserRepository
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
