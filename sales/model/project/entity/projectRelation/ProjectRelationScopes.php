<?php

namespace sales\model\project\entity\projectRelation;

use common\models\Project;

/**
* @see ProjectRelation
*/
class ProjectRelationScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProjectRelation[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProjectRelation|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byProjectId(int $id): self
    {
        return $this->andWhere(['prl_project_id' => $id]);
    }

    public function joinWithRelatedProjectByKey(string $key): self
    {
        return $this->innerJoin(Project::tableName(), 'prl_related_project_id = id and project_key = :projectKey', ['projectKey' => $key]);
    }
}
