<?php

namespace modules\offer\src\helpers;

use yii\helpers\Html;

class OfferHelper
{
    public static function displayAlternativeOfferIcon(): string
    {
        return Html::tag('i', '', [
            'class' => 'fab fa-autoprefixer',
            'title' => 'Alternative Offer',
            'data-toggle' => 'tooltip'
        ]);
    }
}
