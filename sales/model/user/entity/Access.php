<?php

namespace sales\model\user\entity;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;

/**
 * Class Access
 *
 * @property array $groups
 * @property array $groupsList
 * @property array $projects
 * @property array $projectsList
 * @property array $departments
 * @property array $departmentsList
 */
class Access
{
    private $groups;
    private $groupsList;

    private $projects;
    private $projectsList;

    private $departments;
    private $departmentsList;

    /**
     * @param Employee $employee
     */
     public function __construct(Employee $employee)
    {
        $this->groups = $employee->ugsGroups;
        $this->projects = $employee->projects;
        $this->departments = $employee->departments;
    }

    /**
     * @return array
     */
    public function getGroupsList(): array
    {
        if ($this->groupsList !== null) {
            return $this->groupsList;
        }

        $this->groupsList = [];

        /** @var UserGroup $item */
        foreach ($this->groups as $item) {
            $this->groupsList[$item->ug_id] = $item->ug_name;
        }

        return $this->groupsList;
    }

    /**
     * @param int $groupId
     * @return bool
     */
    public function inGroup(int $groupId): bool
    {
        if (!$this->getGroupsList()) {
            return false;
        }
        return array_key_exists($groupId, $this->getGroupsList());
    }

    /**
     * @return array
     */
    public function getProjectsList(): array
    {
        if ($this->projectsList !== null) {
            return $this->projectsList;
        }

        $this->projectsList = [];

        /** @var Project $item */
        foreach ($this->projects as $item) {
            $this->projectsList[$item->id] = $item->name;
        }

        return $this->projectsList;
    }

    /**
     * @param int $projectId
     * @return bool
     */
    public function inProject(int $projectId): bool
    {
        if (!$this->getProjectsList()) {
            return false;
        }
        return array_key_exists($projectId, $this->getProjectsList());
    }

    /**
     * @return array
     */
    public function getDepartmentsList(): array
    {
        if ($this->departmentsList !== null) {
            return $this->departmentsList;
        }

        $this->departmentsList = [];

        /** @var Department $item */
        foreach ($this->departments as $item) {
            $this->departmentsList[$item->dep_id] = $item->dep_name;
        }

        return $this->departmentsList;
    }

    /**
     * @param int $departmentId
     * @return bool
     */
    public function inDepartment(int $departmentId): bool
    {
        if (!$this->getDepartmentsList()) {
            return false;
        }
        return array_key_exists($departmentId, $this->getDepartmentsList());
    }
}
