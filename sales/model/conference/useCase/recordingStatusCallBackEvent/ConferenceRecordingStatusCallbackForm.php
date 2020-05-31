<?php

namespace sales\model\conference\useCase\recordingStatusCallBackEvent;

use yii\base\Model;

/**
 * Class ConferenceRecordingStatusCallbackForm
 *
 * @property $RecordingSource;
 * @property $RecordingSid;
 * @property $ConferenceSid;
 * @property $RecordingUrl;
 * @property $RecordingStatus;
 * @property $RecordingChannels;
 * @property $ErrorCode;
 * @property $RecordingStartTime;
 * @property $AccountSid;
 * @property $RecordingDuration;
 */
class ConferenceRecordingStatusCallbackForm extends Model
{
    /** Twilio parameters. Camel Case */

    public $RecordingSource;
    public $RecordingSid;
    public $ConferenceSid;
    public $RecordingUrl;
    public $RecordingStatus;
    public $RecordingChannels;
    public $ErrorCode;
    public $RecordingStartTime;
    public $AccountSid;
    public $RecordingDuration;

    public function rules(): array
    {
        return [
            ['RecordingSource', 'string'],

            ['RecordingSid', 'string'],

            ['ConferenceSid', 'required'],
            ['ConferenceSid', 'string'],
            ['ConferenceSid', 'filter', 'filter' => static function ($value) {
                return mb_substr($value, 0, 34);
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['RecordingUrl', 'required'],
            ['RecordingUrl', 'string'],

            ['RecordingStatus', 'string'],

            ['RecordingChannels', 'string'],

            ['ErrorCode', 'string'],

            ['RecordingStartTime', 'string'],

            ['AccountSid', 'required'],
            ['AccountSid', 'string'],

            ['RecordingDuration', 'string'],
        ];
    }

    public function filterBool($value): string
    {
        if (!$value || $value !== 'true') {
            return 0;
        }
        return 1;
    }

    public function formName(): string
    {
        return '';
    }
}
