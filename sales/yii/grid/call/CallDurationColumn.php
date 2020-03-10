<?php

namespace sales\yii\grid\call;

use common\models\Call;
use yii\grid\DataColumn;
use yii\helpers\Html;

class CallDurationColumn extends DataColumn
{
    public $attribute = 'c_recording_duration';
    public $label = 'Recording';
    public $format = 'raw';
    public $options = ['style' => 'width: 80px'];
    public $contentOptions = ['class' => 'text-right'];

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var Call $model */
        if (!$model->c_recording_url) {
            return '-';
        }

        if ($model->c_recording_duration && $model->c_recording_duration >= 3600) {
            $format = 'H:i:s';
        } else {
            $format = 'i:s';
        }

        return  Html::button(gmdate($format, $model->c_recording_duration) . ' <i class="fa fa-volume-up"></i>', ['title' => $model->c_recording_duration . ' (sec)', 'class' => 'btn btn-' . ($model->c_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $model->c_recording_url /*yii\helpers\Url::to(['call/record', 'sid' =>  $model->c_call_sid ])*/ ]);
    }

}
