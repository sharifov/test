<?php

namespace modules\qaTask\src\useCases\qaTask\multiple\create;

use yii\bootstrap4\Html;

class SuccessMessage extends Message
{
    public function format(): string
    {
        return Html::tag('span', $this->text, ['style' => 'color: #28a048']);
    }
}
