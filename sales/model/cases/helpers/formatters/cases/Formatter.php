<?php

namespace sales\model\cases\helpers\formatters\cases;

use sales\entities\cases\Cases;
use yii\bootstrap4\Html;

class Formatter
{
    public static function asCase(Cases $cases, ?string $class): string
    {
        $faClass = $class ?? 'fa-arrow-right';

        return Html::tag('i', '', ['class' => 'fa ' . $faClass])
            . ' '
            . Html::a(
                'case: ' . $cases->cs_id,
                ['/cases/view', 'gid' => $cases->cs_gid],
                ['target' => '_blank', 'data-pjax' => 0]
            );
    }
}
