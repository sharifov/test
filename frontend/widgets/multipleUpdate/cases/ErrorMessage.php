<?php

namespace frontend\widgets\multipleUpdate\cases;

use yii\bootstrap4\Html;

class ErrorMessage extends Message
{
    public function format(): string
    {
        return Html::tag('span', $this->text, ['style' => 'color: #c55']);
    }
}
