<?php

namespace common\components\grid\call;

use common\models\Call;
use yii\grid\DataColumn;
use yii\helpers\Html;

class CallDurationColumn extends DataColumn
{
    public $attributeDuration = 'c_recording_duration';
    public $attributeSid = 'c_recording_sid';
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

        return  Html::button(gmdate($format, $model->{$this->attributeDuration}) . ' <i class="fa fa-volume-up"></i>', ['title' => $model->{$this->attributeDuration} . ' (sec)', 'class' => 'btn btn-' . ($model->{$this->attributeDuration} < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $model->{$this->attributeUrl} /*yii\helpers\Url::to(['call/record', 'sid' =>  $model->c_call_sid ])*/ ]);
    }
}
