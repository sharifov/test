<?php

namespace sales\repositories\user;

use common\models\Employee;
use sales\repositories\NotFoundException;

/**
 * Class UserRepository
 */
class UserRepository
{
    /**
     * @param $id
     * @return Employee
     */
    public function get($id): Employee
    {
        if ($user = Employee::findOne($id)) {
            return $user;
        }
        throw new NotFoundException('User is not found');
    }

}