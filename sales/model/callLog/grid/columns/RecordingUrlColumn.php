<?php

namespace sales\model\callLog\grid\columns;

use sales\helpers\call\CallHelper;
use sales\model\callLog\entity\callLog\CallLog;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\Html;

class RecordingUrlColumn extends DataColumn
{
    const AUDIO_TAG = 1;
    const AUDIO_BUTTON = 2;

    public $attribute = 'cl_duration';
    public $label = 'Recording';
    public $format = 'raw';
    public $options = ['style' => 'width: 80px'];
    public $contentOptions = ['class' => 'text-right'];
    public $audioContent = self::AUDIO_BUTTON;
    public $audioContentOptions = [
        'controls' => 'controls',
        'controlslist' => 'nodownload',
        'style' => 'width: 350px; height: 25px'
    ];

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var CallLog $model */
        if (!$model->record || !$model->record->clr_record_sid) {
            return '-';
        }

        if ($model->record->clr_duration && $model->record->clr_duration >= 3600) {
            $format = 'H:i:s';
        } else {
            $format = 'i:s';
        }

        if ($this->audioContent === self::AUDIO_BUTTON) {
            return CallHelper::displayAudioBtn($model->recordingUrl, $format, (int)$model->record->clr_duration, $model->cl_call_sid);
        }

        if ($this->audioContent === self::AUDIO_TAG) {
            return CallHelper::displayAudioTag($model->recordingUrl, (string)$model->cl_call_sid, $this->audioContentOptions);
        }

        throw new InvalidConfigException('AudioContent value is not valid');
    }
}
