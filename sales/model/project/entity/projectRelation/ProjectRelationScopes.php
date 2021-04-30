<?php

namespace sales\model\project\entity\projectRelation;

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
}
