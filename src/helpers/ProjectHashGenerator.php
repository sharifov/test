<?php

namespace src\helpers;

use common\models\Project;

class ProjectHashGenerator
{
    public static function getHashByProjectApiKey(string $projectApiKey, string $value): string
    {
        return hash('sha256', sprintf("%s:%s", $projectApiKey, $value));
    }

    public static function getHashByProjectId(int $projectId, string $value): string
    {
        $projectApiKey = Project::find()->select(['api_key'])->byId($projectId)->scalar();
        if (!$projectApiKey) {
            throw new \DomainException('Not found Project API Key. ProjectId: ' . $projectId);
        }
        return self::getHashByProjectApiKey($projectApiKey, $value);
    }
}
