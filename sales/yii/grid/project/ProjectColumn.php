<?php

namespace sales\yii\grid\project;

use common\models\Project;
use yii\grid\DataColumn;

/**
 * Class ProjectColumn
 *
 * @property $relation
 *
 * Ex.
    [
        'class' => \sales\yii\grid\project\ProjectColumn::class,
        'attribute' => 'dpp_project_id',
        'relation' => 'dppProject',
    ],
 *
 */
class ProjectColumn extends DataColumn
{
    public $format = 'projectName';

    public $relation;

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if ($this->filter === null) {
            $this->filter = \common\models\Project::getList();
        }
    }

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($entity = $model->{$this->relation})) {
            /** @var Project $entity */
            return $entity->name;
        }

        return null;
    }
}
