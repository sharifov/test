<?php

namespace modules\lead\src\helpers\formatters\lead;

use common\models\Lead;
use yii\bootstrap4\Html;

class Formatter
{
    public static function asLead(Lead $lead, ?string $class): string
    {
        $faClass = $class ?? 'fa-arrow-right';

        return Html::tag('i', '', ['class' => 'fa ' . $faClass])
            . ' '
            . Html::a(
                'lead: ' . $lead->id,
                ['/lead/view', 'gid' => $lead->gid],
                ['target' => '_blank', 'data-pjax' => 0]
            );
    }
}
