<?php

namespace modules\qaTask\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class QaTaskColumn
 *
 * @property $relation
 *
 *  Ex.
    [
        'class' => modules\qaTask\src\grid\columns\QaTaskColumn::class,
        'attribute' => 'tsl_task_id',
        'relation' => 'task',
    ],
 */
class QaTaskColumn extends DataColumn
{
    public $relation;

    public function init(): void
    {
        parent::init();
        if (!$this->relation) {
            throw new \InvalidArgumentException('relation must be set.');
        }
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        if ($model->{$this->attribute}) {
            return $this->grid->formatter->format($model->{$this->relation}, 'qaTask');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
