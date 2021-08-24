<?php

namespace sales\model\project\entity\projectRelation;

class ProjectRelationQuery
{
    public static function findByRelatedProjectKey(int $projectId, string $relatedProjectKey): ?ProjectRelation
    {
        $query = ProjectRelation::find();
        $query->byProjectId($projectId);
        $query->joinWithRelatedProjectByKey($relatedProjectKey);
        return $query->one();
    }

    /**
     * @param int $projectId
     * @return int[]
     */
    public static function getRelatedProjectIds(int $projectId): array
    {
        $query = ProjectRelation::find();
        $query->select(['prl_related_project_id']);
        $query->byProjectId($projectId);
        return $query->asArray()->column();
    }
}
