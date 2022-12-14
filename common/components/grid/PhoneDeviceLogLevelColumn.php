<?php

namespace common\components\grid;

use src\model\voip\phoneDevice\log\PhoneDeviceLogLevel;
use yii\grid\DataColumn;

/**
 * Class QaTaskObjectTypeColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\PhoneDeviceLogLevelColumn::class,
    ],
 */
class PhoneDeviceLogLevelColumn extends DataColumn
{
    public $format = 'phoneDeviceLogLevel';
    public $attribute = 'pdl_level';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = PhoneDeviceLogLevel::getList();
        }
    }
}
