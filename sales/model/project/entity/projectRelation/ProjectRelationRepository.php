<?php

namespace sales\model\project\entity\projectRelation;

use sales\helpers\ErrorsToStringHelper;
use sales\repositories\NotFoundException;

/**
 * Class ProjectRelationRepository
 */
class ProjectRelationRepository
{
    public function save(ProjectRelation $model): void
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('ProjectRelation save failed');
        }
    }

    public function replaceRelations(int $projectId, ?array $relationIds): array
    {
        $result = [];
        $relationIds = $relationIds ?? [];
        ProjectRelation::deleteAll(['prl_project_id' => $projectId]);
        foreach ($relationIds as $relationId) {
            $projectRelation = ProjectRelation::create($projectId, $relationId);
            if (!$projectRelation->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($projectRelation));
            }
            $this->save($projectRelation);
            $result[$relationId] = $projectRelation;
        }
        return $result;
    }

    public function findByRelatedProjectKey(int $projectId, string $relatedProjectKey): ProjectRelation
    {
        if ($relation = ProjectRelationQuery::findByRelatedProjectKey($projectId, $relatedProjectKey)) {
            return $relation;
        }
        throw new NotFoundException('Not found project relation by key: ' . $relatedProjectKey);
    }
}
