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
}
