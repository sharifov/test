<?php

namespace common\components\grid\call;

use common\models\Call;
use sales\helpers\call\CallHelper;
use yii\grid\DataColumn;
use yii\helpers\Html;

class CallDurationColumn extends DataColumn
{
    public $attributeDuration = 'c_recording_duration';
    public $attributeSid = 'c_recording_sid';
    public $attributeCallSid = 'c_call_sid';
    public $attributeUrl = 'recordingUrl';
    public $label = 'Recording';
    public $format = 'raw';
    public $options = ['style' => 'width: 80px'];
    public $contentOptions = ['class' => 'text-right'];

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var Call $model */
        if (!$model->{$this->attributeSid}) {
            return '-';
        }

        if ($model->{$this->attributeDuration} && $model->{$this->attributeDuration} >= 3600) {
            $format = 'H:i:s';
        } else {
            $format = 'i:s';
        }

        return CallHelper::displayAudioBtn((string)$model->{$this->attributeUrl}, $format, (int)$model->{$this->attributeDuration}, $model->{$this->attributeCallSid});
    }
}
