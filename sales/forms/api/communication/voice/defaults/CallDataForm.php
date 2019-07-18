<?php

namespace sales\forms\api\communication\voice\defaults;

use yii\base\Model;

/**
 * Class CallDataForm
 * @property $Called;
 * @property $ParentCallSid;
 * @property $RecordingUrl;
 * @property $ToState;
 * @property $CallerCountry;
 * @property $Direction;
 * @property $Timestamp;
 * @property $CallbackSource;
 * @property $CallerState;
 * @property $CalledVia;
 * @property $ToZip;
 * @property $SequenceNumber;
 * @property $To;
 * @property $CallSid;
 * @property $ToCountry;
 * @property $CallerZip;
 * @property $CalledZip;
 * @property $ApiVersion;
 * @property $CallStatus;
 * @property $CalledCity;
 * @property $RecordingSid;
 * @property $Duration;
 * @property $From;
 * @property $CallDuration;
 * @property $AccountSid;
 * @property $CalledCountry;
 * @property $ApplicationSid;
 * @property $CallerCity;
 * @property $ToCity;
 * @property $FromCountry;
 * @property $Caller;
 * @property $FromCity;
 * @property $CalledState;
 * @property $ForwardedFrom;
 * @property $FromZip;
 * @property $FromState;
 * @property $RecordingDuration;
 * @property $com_call_id;
 */
class CallDataForm extends Model
{

    public $Called;
    public $ParentCallSid;
    public $RecordingUrl;
    public $ToState;
    public $CallerCountry;
    public $Direction;
    public $Timestamp;
    public $CallbackSource;
    public $CallerState;
    public $CalledVia;
    public $ToZip;
    public $SequenceNumber;
    public $To;
    public $CallSid;
    public $ToCountry;
    public $CallerZip;
    public $CalledZip;
    public $ApiVersion;
    public $CallStatus;
    public $CalledCity;
    public $RecordingSid;
    public $Duration;
    public $From;
    public $CallDuration;
    public $AccountSid;
    public $CalledCountry;
    public $ApplicationSid;
    public $CallerCity;
    public $ToCity;
    public $FromCountry;
    public $Caller;
    public $FromCity;
    public $CalledState;
    public $ForwardedFrom;
    public $FromZip;
    public $FromState;
    public $RecordingDuration;
    public $com_call_id;

    /**
     * @return bool
     */
    public function isEmptyCallSid(): bool
    {
        return $this->CallSid ? false : true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['Called', 'string'],
            ['ParentCallSid', 'string'],
            ['RecordingUrl', 'string'],
            ['ToState', 'string'],
            ['CallerCountry', 'string'],
            ['Direction', 'in', 'range' => ['outbound-dial']],
            ['Timestamp', 'string'],
            ['CallbackSource', 'in', 'range' => ['call-progress-events']],
            ['CallerState', 'string'],
            ['CalledVia', 'string'],
            ['ToZip', 'string'],
            ['SequenceNumber', 'integer'],
            ['To', 'string'],
            ['CallSid', 'string'],
            ['ToCountry', 'string'],
            ['CallerZip', 'string'],
            ['CalledZip', 'string'],
            ['ApiVersion', 'date', 'format' => 'Y-m-d'],
            ['CallStatus', 'in', 'range' => ['completed', 'canceled']],
            ['CalledCity', 'string'],
            ['RecordingSid', 'string'],
            ['Duration', 'integer'],
            ['From', 'string'],
            ['CallDuration', 'integer'],
            ['AccountSid', 'string'],
            ['CalledCountry', 'string'],
            ['ApplicationSid', 'string'],
            ['CallerCity', 'string'],
            ['ToCity', 'string'],
            ['FromCountry', 'string'],
            ['Caller', 'string'],
            ['FromCity', 'string'],
            ['CalledState', 'string'],
            ['ForwardedFrom', 'string'],
            ['FromZip', 'string'],
            ['FromState', 'string'],
            ['RecordingDuration', 'integer'],
            ['com_call_id', 'integer'],
        ];
    }

}