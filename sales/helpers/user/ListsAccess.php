<?php

namespace sales\helpers\user;

use common\models\Employee;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;

/**
 * Class UserLists
 *
 * @property int $userId
 * @property array $projects
 * @property array $departments
 * @property array $users
 */
class UserLists
{

    private $userId;

    private $projects;
    private $departments;
    private $users;

    /**
     * @param int|null $userId
     */
    public function __construct(?int $userId = null)
    {
        $this->userId = UserFinder::find($userId);
    }

    /**
     * @return array
     */
    public function getDepartments(): array
    {
        if ($this->departments !== null) {
            return $this->departments;
        }
        $this->departments = EmployeeDepartmentAccess::getDepartments($this->userId);
        return $this->departments;
    }


    /**
     * @return array
     */
    public function getProjects(): array
    {
        if ($this->projects !== null) {
            return $this->projects;
        }
        $this->projects = EmployeeProjectAccess::getProjects($this->userId);
        return $this->projects;
    }

}