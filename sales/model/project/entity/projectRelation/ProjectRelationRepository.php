<?php

namespace sales\model\project\entity\projectRelation;

use sales\helpers\ErrorsToStringHelper;

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
}
