<?php

namespace sales\repositories\department;

use common\models\Department;
use sales\repositories\Repository;

class DepartmentRepository extends Repository
{
    public function find(int $id)
    {
        return Department::findOne($id);
    }

    public function findByName(string $name)
    {
        return Department::findOne(['dep_name' => $name]);
    }
}
