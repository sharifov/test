<?php

namespace sales\model\phoneList\helpers\formatters;

use sales\model\phoneList\entity\PhoneList;
use yii\bootstrap4\Html;

class PhoneListFormatter
{
    public static function asFormat(PhoneList $phoneList): string
    {
        if ($phoneList->pl_enabled) {
            return $phoneList->pl_phone_number;
        }
        return  Html::tag('span', $phoneList->pl_phone_number, ['style' => 'color:red']);
    }
}
