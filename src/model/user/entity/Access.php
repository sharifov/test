<?php

namespace src\model\user\entity;

use common\models\Employee;

/**
 * Class Access
 *
 * @property Employee $user
 * @property AccessCache $cache
 * @property array|null $projects
 * @property array|null $departments
 * @property array|null $groups
 */
class Access
{
    private $user;
    private $cache;

    private $groups;
    private $projects;
    private $departments;
    private $skill;

    public function __construct(Employee $user, AccessCache $cache)
    {
        $this->user = $user;
        $this->cache = $cache;
    }

    public function getUserId(): int
    {
        return $this->user->id;
    }

    public function getUserName(): int
    {
        return $this->user->username;
    }

    public function getAllProjects(?string $key = 'name'): array
    {
        return array_column($this->getProjects(), $key, 'id');
    }

    public function getActiveProjects(): array
    {
        return array_column(array_filter($this->getProjects(), static function ($v) {
            return !$v['closed'];
        }, ARRAY_FILTER_USE_BOTH), 'name', 'id');
    }

    private function getProjects(): array
    {
        if ($this->projects !== null) {
            return $this->projects;
        }

        if ($projects = $this->cache->getProjects()) {
            $this->projects = $projects;
            return $this->projects;
        }

        $this->projects = $this->user->getProjectsToArray();

        return $this->projects;
    }

    public function getDepartments(): array
    {
        if ($this->departments !== null) {
            return $this->departments;
        }

        if ($departments = $this->cache->getDepartments()) {
            $this->departments = $departments;
            return $this->departments;
        }

        $this->departments = $this->user->getDepartmentsToArray();

        return $this->departments;
    }

    public function getAllGroups(): array
    {
        return array_column($this->getGroups(), 'ug_name', 'ug_id');
    }

    public function getAllDepartments(): array
    {
        return array_column($this->getDepartments(), 'dep_key', 'dep_id');
    }

    public function getActiveGroups(): array
    {
        return array_column(array_filter($this->getGroups(), static function ($v) {
            return !$v['ug_disable'];
        }, ARRAY_FILTER_USE_BOTH), 'ug_name', 'ug_id');
    }

    private function getGroups(): array
    {
        if ($this->groups !== null) {
            return $this->groups;
        }

        if ($groups = $this->cache->getGroups()) {
            $this->groups = $groups;
            return $this->groups;
        }

        $this->groups = $this->user->getGroupsToArray();

        return $this->groups;
    }

    public function getSkill(): ?string
    {
        if ($this->skill === null) {
            $this->skill = $this->cache->getSkill();
        }

        if ($this->skill === null) {
            $this->skill = $this->user->userProfile->up_skill;
        }

        return $this->skill;
    }
}
