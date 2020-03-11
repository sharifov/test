<?php

namespace sales\cache\app;

use common\models\Department;
use common\models\Project;
use common\models\UserGroup;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

/**
 * Class AppCache
 *
 * @property CacheInterface $cache
 */
class AppCache
{
    private const APP_DEPENDENCY_TAG = 'cache_app';
    private const PROJECTS = 'cache_app_projects';
    private const DEPARTMENTS = 'cache_app_departments';
    private const USER_GROUPS = 'cache_app_user_groups';

    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getProjects(): array
    {
        return $this->cache->getOrSet(self::PROJECTS, static function () {
            return Project::find()->select(['id', 'name', 'closed'])->asArray()->all();
        }, 0, new TagDependency(['tags' => self::APP_DEPENDENCY_TAG]));
    }

    public function getDepartments(): array
    {
        return $this->cache->getOrSet(self::DEPARTMENTS, static function () {
            return Department::find()->select(['dep_id', 'dep_key', 'dep_name'])->asArray()->all();
        }, 0, new TagDependency(['tags' => self::APP_DEPENDENCY_TAG]));
    }

    public function getUserGroups(): array
    {
        return $this->cache->getOrSet(self::USER_GROUPS, static function () {
            $groups = [];
            foreach (UserGroup::find()->with(['userGroupAssigns'])->all() as $group) {
                $groups[$group->ug_id] = [
                    'ug_id' => $group->ug_id,
                    'ug_key' => $group->ug_key,
                    'ug_name' => $group->ug_name,
                    'ug_disable' => $group->ug_disable,
                    'users' => ArrayHelper::getColumn($group->userGroupAssigns, 'ugs_user_id')
                ];
            }
            return $groups;
        }, 0, new TagDependency(['tags' => self::APP_DEPENDENCY_TAG]));
    }

    public function getUsersFromGroups(array $groupsId): array
    {
        $groups = array_filter($this->getUserGroups(), static function ($k) use ($groupsId) {
            return in_array($k, $groupsId, false);
        }, ARRAY_FILTER_USE_KEY);
        $users = [];
        foreach (ArrayHelper::getColumn($groups, 'users') as $item) {
            foreach ($item as $user) {
                $users[$user] = $user;
            }
        }
        return $users;
    }

    public function flush(): void
    {
        TagDependency::invalidate($this->cache, self::APP_DEPENDENCY_TAG);
    }
}
