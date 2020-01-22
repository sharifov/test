<?php

namespace modules\lead\helpers\formatters\lead;

use common\models\Lead;
use yii\bootstrap4\Html;

class Formatter
{
    public static function asLead(Lead $lead): string
    {
        return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
            . ' '
            . Html::a(
                'lead: ' . $lead->id,
                ['lead/view', 'gid' => $lead->gid],
                ['target' => '_blank', 'data-pjax' => 0]
            );
    }
}
