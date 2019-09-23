<?php

namespace frontend\extensions;

/**
 * Class DatePicker
 */
class DatePicker extends \dosamigos\datepicker\DatePicker
{
    public $clientOptions = [
        'autoclose' => true,
        'format' => 'dd-M-yyyy',
        'todayBtn' => true
    ];
}
