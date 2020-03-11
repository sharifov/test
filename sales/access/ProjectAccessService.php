<?php

namespace sales\access;

use common\models\Project;
use sales\model\user\entity\Access;
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

    public function checkAccess(Access $access, int $projectId): bool
    {
        if ($this->accessChecker->checkAccess($access->getUserId(), self::PERMISSION)) {
            return true;
        }

        return array_key_exists($projectId, $access->getActiveProjects());
    }

    public function guard(Access $access, int $projectId): void
    {
        if (!$this->checkAccess($access, $projectId)) {
            throw new \DomainException('User: ' . $access->getUserName() . ' cant access to projectId: ' . $projectId . '.');
        }
    }

    public function getProjects(Access $access): array
    {
        if ($this->accessChecker->checkAccess($access->getUserId(), self::PERMISSION)) {
            return Project::find()->select(['name'])->indexBy('id')->column();
        }

        return $access->getActiveProjects();
    }

    public function processQuery(Access $access, ProjectQueryInterface $query): void
    {
        if ($this->accessChecker->checkAccess($access->getUserId(), self::PERMISSION)) {
            return;
        }

        $query->projects(array_keys($access->getActiveProjects()));
    }
}
