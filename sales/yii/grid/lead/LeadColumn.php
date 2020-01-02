<?php

namespace sales\yii\grid\lead;

use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * Class LeadColumn
 *
 * @property $relation
 */
class LeadColumn extends DataColumn
{
    public $relation;

    public function init()
    {
        parent::init();
        if (!$this->relation) {
            throw new \InvalidArgumentException('relation must be set.');
        }
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($leadId = $model->{$this->attribute}) {
            return '<i class="fa fa-arrow-right"></i> ' . Html::a(
                'lead: ' . $leadId,
                ['lead/view', 'gid' => $model->{$this->relation}->gid],
                ['target' => '_blank', 'data-pjax' => 0]);
        }
        return '';
    }
}
