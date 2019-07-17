<?php

namespace sales\forms\api\communication\voice\record;

use yii\base\Model;

/**
 * Class CallDataForm
 * @property $RecordingSource;
 * @property $RecordingSid;
 * @property $RecordingUrl;
 * @property $RecordingStatus;
 * @property $RecordingChannels;
 * @property $ErrorCode;
 * @property $CallSid;
 * @property $RecordingStartTime;
 * @property $AccountSid;
 * @property $RecordingDuration;
 */
class CallDataForm extends Model
{
    public $RecordingSource;
    public $RecordingSid;
    public $RecordingUrl;
    public $RecordingStatus;
    public $RecordingChannels;
    public $ErrorCode;
    public $CallSid;
    public $RecordingStartTime;
    public $AccountSid;
    public $RecordingDuration;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['RecordingSource', 'in', 'range' => ['DialVerb']],
            ['RecordingSid', 'string'],
            ['RecordingUrl', 'string'],
            ['RecordingStatus', 'in', 'range' => ['completed']],
            ['RecordingChannels', 'integer'],
            ['ErrorCode', 'integer'],
            ['CallSid', 'string'],
            ['RecordingStartTime', 'safe'],
            ['AccountSid', 'string'],
            ['RecordingDuration', 'integer'],
        ];
    }

    /**
     * @return bool
     */
    public function isEmptyCallSid(): bool
    {
        return $this->CallSid ? false : true;
    }

    /**
     * @return bool
     */
    public function isEmptyRecordingUrl(): bool
    {
        return $this->RecordingUrl ? false : true;
    }
}
