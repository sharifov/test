<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class ProjectEmployeeAccessQuery
 * @package common\models\query
 */
class ProjectEmployeeAccessQuery extends ActiveQuery
{
    public function usersByProject($projectId): ProjectEmployeeAccessQuery
    {
        return $this->select(['employee_id'])->andWhere(['project_id' => $projectId]);
    }
}
