<?php

namespace sales\model\user\entity;

use common\models\Department;
use common\models\Employee;
use yii\helpers\VarDumper;

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

    public function getAllProjects(): array
    {
        return array_column($this->getProjects(), 'name', 'id');
    }

    public function getActiveProjects(): array
    {
        return array_column(array_filter($this->getProjects(), static function ($v) { return !$v['closed']; }, ARRAY_FILTER_USE_BOTH), 'name', 'id');
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

        $this->projects = [];

        foreach ($this->user->getProjects()->select(['name', 'closed', 'id'])->indexBy('id')->asArray()->all() as $key => $item) {
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

    public function getAllGroups(): array
    {
        return array_column($this->getGroups(), 'ug_name', 'ug_id');
    }

    public function getActiveGroups(): array
    {
        return array_column(array_filter($this->getGroups(), static function ($v) { return !$v['ug_disable']; }, ARRAY_FILTER_USE_BOTH), 'ug_name', 'ug_id');
    }

    private function getGroups(): array
    {
        if ($this->groups !== null) {
            return $this->groups;
        }

        $this->groups = [];

        foreach ($this->user->getUgsGroups()->select(['ug_id', 'ug_name', 'ug_disable'])->indexBy('ug_id')->asArray()->all() as $key => $item) {
            $this->groups[$key] = $item;
        }

        return $this->groups;
    }
}
