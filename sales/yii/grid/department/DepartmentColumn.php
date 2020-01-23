<?php

namespace sales\yii\grid\department;

use common\models\Department;
use yii\grid\DataColumn;

/**
 * Class DepartmentColumn
 *
 * @property $relation
 *
 * Ex.
    [
        'class' => \sales\yii\grid\department\DepartmentColumn::class,
        'attribute' => 'dpp_dep_id',
        'relation' => 'dppDep',
    ],
 *
 */
class DepartmentColumn extends DataColumn
{
    public $format = 'departmentName';

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
        if ($model->{$this->attribute} && ($entity = $model->{$this->relation})) {
            /** @var Department $entity */
            return $entity->dep_name;
        }

        return null;
    }
}
