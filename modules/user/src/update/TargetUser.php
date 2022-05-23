<?php

namespace modules\user\src\update;

use common\models\Employee;

/**
 * Class TargetUser
 *
 * @property Employee $targetUser
 * @property Employee $updaterUser
 * @property bool|null $sameUser
 * @property bool|null $sameGroup
 * @property bool|null $sameDepartment
 * @property array|null $roles
 * @property array|null $projects
 * @property array|null $groups
 * @property array|null $departments
 */
class TargetUser
{
    private Employee $targetUser;
    private Employee $updaterUser;
    private ?bool $sameUser = null;
    private ?bool $sameGroup = null;
    private ?bool $sameDepartment = null;
    private ?array $roles = null;
    private ?array $projects = null;
    private ?array $groups = null;
    private ?array $departments = null;

    public function __construct(Employee $targetUser, Employee $updaterUser)
    {
        $this->targetUser = $targetUser;
        $this->updaterUser = $updaterUser;
    }

    public function isSameUser(): bool
    {
        if ($this->sameUser !== null) {
            return $this->sameUser;
        }
        $this->sameUser = $this->targetUser->isSameUser($this->updaterUser);
        return $this->sameUser;
    }

    public function isSameGroup(): bool
    {
        if ($this->sameGroup !== null) {
            return $this->sameGroup;
        }
        $this->sameGroup = $this->targetUser->isSameUserGroup(array_keys($this->updaterUser->getUserGroupList()));
        return $this->sameGroup;
    }

    public function isSameDepartment(): bool
    {
        if ($this->sameDepartment !== null) {
            return $this->sameDepartment;
        }
        $this->sameDepartment = $this->targetUser->isSameDepartment(array_keys($this->updaterUser->getUserDepartmentList()));
        return $this->sameDepartment;
    }

    public function getUsername(): string
    {
        return $this->targetUser->username;
    }

    public function getRoles(): array
    {
        if ($this->roles !== null) {
            return $this->roles;
        }
        $this->roles = $this->targetUser->getRoles(true);
        return $this->roles;
    }

    public function getProjects(): array
    {
        if ($this->projects !== null) {
            return $this->projects;
        }
        $this->projects = $this->targetUser->access->getAllProjects('key');
        return $this->projects;
    }

    public function getGroups(): array
    {
        if ($this->groups !== null) {
            return $this->groups;
        }
        $this->groups = $this->targetUser->access->getAllGroups();
        return $this->groups;
    }

    public function getDepartments(): array
    {
        if ($this->departments !== null) {
            return $this->departments;
        }
        $this->departments = $this->targetUser->access->getAllDepartments();
        return $this->departments;
    }
}
