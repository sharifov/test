<?php

namespace sales\model\callLog\grid\columns;

use sales\model\callLog\entity\callLog\CallLog;
use yii\grid\DataColumn;
use yii\helpers\Html;

class RecordingUrlColumn extends DataColumn
{
    public $attribute = 'cl_duration';
    public $label = 'Recording';
    public $format = 'raw';
    public $options = ['style' => 'width: 80px'];
    public $contentOptions = ['class' => 'text-right'];

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var CallLog $model */
        if (!$model->record || !$model->record->clr_record_sid) {
            return '-';
        }

        if ($model->cl_duration && $model->cl_duration >= 3600) {
            $format = 'H:i:s';
        } else {
            $format = 'i:s';
        }

        return Html::button(
            gmdate($format, $model->cl_duration) . ' <i class="fa fa-volume-up"></i>',
            [
                'title' => $model->cl_duration . ' (sec)', 'class' => 'btn btn-' . ($model->cl_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url',
                'data-source_src' => $model->record->getRecordingUrl()
            ]
        );
    }
}
