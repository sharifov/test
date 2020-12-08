<?php

namespace frontend\widgets;

class DateTimePickerWidget extends \dosamigos\datetimepicker\DateTimePicker
{
    public function registerClientScript()
    {
        $this->language = null;
        parent::registerClientScript();
    }
}
