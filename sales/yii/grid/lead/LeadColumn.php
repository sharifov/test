<?php

namespace sales\yii\grid\lead;

use yii\grid\DataColumn;
use yii\bootstrap4\Html;

/**
 * Class LeadColumn
 *
 * @property $relation
 *
 *  Ex.
    [
        'class' => \sales\yii\grid\lead\LeadColumn::class,
        'attribute' => 'lead_id',
        'relation' => 'lead',
    ],
 *
 */
class LeadColumn extends DataColumn
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
        if ($leadId = $model->{$this->attribute}) {

            return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
                . ' '
                . Html::a(
                    'lead: ' . $leadId,
                    ['lead/view', 'gid' => $model->{$this->relation}->gid],
                    ['target' => '_blank', 'data-pjax' => 0]
                );
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
