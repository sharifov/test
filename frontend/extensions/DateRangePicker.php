<?php

namespace frontend\extensions;

/**
 * Class DateRangePicker
 */
class DateRangePicker extends \kartik\daterange\DateRangePicker
{
    public $useWithAddon = true;

    public $presetDropdown = true;

    public $hideInput = true;

    public $convertFormat = true;

    public $pluginOptions = [
        'timePicker' => true,
        'timePickerIncrement' => 1,
        'timePicker24Hour' => true,
        'locale' => [
            'format' => 'Y-m-d H:i',
            'separator' => ' - '
        ]
    ];
}
