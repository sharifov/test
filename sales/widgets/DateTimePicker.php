<?php

namespace sales\widgets;

class DateTimePicker extends \dosamigos\datetimepicker\DateTimePicker
{
    public $clientOptions = [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayBtn' => true
    ];
}
