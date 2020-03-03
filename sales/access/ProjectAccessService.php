<?php

namespace sales\access;

use common\models\Employee;
use common\models\Project;
use yii\rbac\CheckAccessInterface;

/**
 * Class ProjectAccessService
 *
 * @property CheckAccessInterface $accessChecker
 */
class ProjectAccessService
{
    private const PERMISSION = 'access/project/all';

    private $accessChecker;

    public function __construct(CheckAccessInterface $accessChecker)
    {
        $this->accessChecker = $accessChecker;
    }

    public function checkAccess(Employee $user, int $projectId): bool
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return true;
        }

        return array_key_exists($projectId, $user->access->getProjects());
    }

    public function guard(Employee $user, int $projectId): void
    {
        if (!$this->checkAccess($user, $projectId)) {
            throw new \DomainException('User: ' . $user->username . ' cant access to projectId: ' . $projectId . '.');
        }
    }

    public function getProjects(Employee $user): array
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return Project::find()->select(['name'])->indexBy('id')->column();
        }

        return $user->access->getProjects();
    }

    public function processQuery(Employee $user, ProjectQueryInterface $query): void
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return;
        }

        $query->projects(array_keys($user->access->getProjects()));
    }
}
