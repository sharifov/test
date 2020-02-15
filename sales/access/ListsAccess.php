<?php

namespace sales\access;

use common\models\Employee;
use common\models\Project;
use sales\helpers\user\UserFinder;

/**
 * Class UserLists
 *
 * @property Employee $user
 * @property array|null $projects
 * @property array|null $sources
 * @property array|null $departments
 * @property array|null $employees
 */
class ListsAccess
{

    private $user;

    private $projects;
    private $sources;
    private $departments;
    private $employees;

    /**
     * @param int|null $userId
     */
    public function __construct(?int $userId = null)
    {
        $this->user = UserFinder::find($userId);
    }

    /**
     * @return array
     */
    public function getDepartments(): array
    {
        if ($this->departments !== null) {
            return $this->departments;
        }
        $this->departments = EmployeeDepartmentAccess::getDepartments($this->user->id);
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
        $this->projects = EmployeeProjectAccess::getProjects($this->user->id);
        return $this->projects;
    }

    /**
     * @param bool $showHidden
     * @return array
     */
    public function getSources($showHidden = false): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }

        $this->sources = [];

        if ($projectsIds = array_keys($this->getProjects())) {
            foreach (Project::find()->andWhere(['id' => $projectsIds])->orderBy('name')->with('sources')->all() as $project) {
                $map = [];
                foreach ($project->sources as $source) {
                    if ($showHidden === false && $source->hidden) {
                        continue;
                    }
                    $map[$source->id] = $source->name;
                }
                if ($map) {
                    $this->sources[$project->name] = $map;
                }
            }
        }

        return $this->sources;
    }

    /**
     * @param bool $withRoles
     * @return array
     */
    public function getEmployees($withRoles = false): array
    {
        if ($this->employees !== null) {
            return $this->employees;
        }
        if ($this->user->isAdmin()) {
            if ($withRoles) {
                $this->employees = Employee::getActiveUsersListWithRoles();
            } else {
                $this->employees = Employee::getActiveUsersList();
            }
        } elseif ($this->user->isAnySupervision()) {
            $this->employees = Employee::getActiveUsersListFromCommonGroups($this->user->id);
        } elseif ($this->user->isQa()) {
            $this->employees = Employee::getActiveUsersList();
        } else {
            $this->employees = [];
        }
        return $this->employees;
    }

}
