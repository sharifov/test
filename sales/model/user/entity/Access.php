<?php

namespace sales\model\user\entity;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;

/**
 * Class Access
 *
 * @property Employee $user
 * @property array|null $projects
 * @property array|null $departments
 * @property array|null $groups
 */
class Access
{
    private $user;
    private $groups;
    private $projects;
    private $departments;

     public function __construct(Employee $user)
    {
        $this->user = $user;
    }

    public function getProjects(): array
    {
        if ($this->projects !== null) {
            return $this->projects;
        }

        $this->projects = [];

        $alias = Project::tableName();

        foreach ($this->user->getProjects()->select([$alias . '.name'])->active()->indexBy($alias . '.id')->column() as $key => $item) {
            $this->projects[$key] = $item;
        }

        return $this->projects;
    }

    public function getDepartments(): array
    {
        if ($this->departments !== null) {
            return $this->departments;
        }

        $this->departments = [];

        $alias = Department::tableName();

        foreach ($this->user->getUdDeps()->select([$alias . '.dep_name'])->indexBy($alias . '.dep_id')->column() as $key => $item) {
            $this->departments[$key] = $item;
        }

        return $this->departments;
    }

    public function getGroups(): array
    {
        if ($this->groups !== null) {
            return $this->groups;
        }

        $this->groups = [];

        $alias = UserGroup::tableName();

        foreach ($this->user->getUgsGroups()->select([$alias . '.ug_name'])->enabled()->indexBy($alias . '.ug_id')->column() as $key => $item) {
            $this->groups[$key] = $item;
        }

        return $this->groups;
    }
}
