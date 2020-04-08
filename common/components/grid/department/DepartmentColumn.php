<?php

namespace common\components\grid\department;

use common\models\Department;
use yii\grid\DataColumn;

/**
 * Class DepartmentColumn
 *
 * @property $relation
 *
 * Ex.
    [
        'class' => \common\components\grid\department\DepartmentColumn::class,
        'attribute' => 'dpp_dep_id',
        'relation' => 'dppDep',
    ],
 *
 */
class DepartmentColumn extends DataColumn
{
    public $format = 'department';

    public $relation;

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if ($this->filter === null) {
            $this->filter = \common\models\Department::getList();
        }
    }

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($department = $model->{$this->relation})) {
            /** @var Department $department */
            return $department->dep_id;
        }

        return null;
    }
}
