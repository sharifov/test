<?php

namespace sales\model\user\entity;

use common\models\Employee;
use yii\caching\Cache;
use yii\caching\TagDependency;

/**
 * Class UserCache
 *
 * @property Employee $user
 * @property Cache $cache
 * @property string $tag
 */
class UserCache
{
    private $user;
    private $cache;
    private $tag;

    public function __construct(Employee $user, Cache $cache)
    {
        $this->user = $user;
        $this->cache = $cache;
        $this->tag = 'user-' . $this->user->id;
    }

    public function getData(): array
    {
        return [
            'projects' => $this->getProjects(),
            'departments' => $this->getDepartments(),
            'groups' => $this->getGroups(),
        ];
    }

    public function flush(): void
    {
        TagDependency::invalidate($this->cache, $this->tag);
    }

    private function getProjects(): array
    {
        return $this->cache->getOrSet($this->tag . '-projects', function () {
            return $this->user->getProjectsToArray();
        }, 0, new TagDependency(['tags' => $this->tag]));
    }

    private function getDepartments(): array
    {
        return $this->cache->getOrSet($this->tag . '-departments', function () {
            return $this->user->getDepartmentsToArray();
        }, 0, new TagDependency(['tags' => $this->tag]));
    }

    private function getGroups(): array
    {
        return $this->cache->getOrSet($this->tag . '-groups', function () {
            return $this->user->getGroupsToArray();
        }, 0, new TagDependency(['tags' => $this->tag]));
    }
}
