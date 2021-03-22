<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use yii\base\Model;

/**
 * Class ConferenceStatusCallbackForm
 *
 * @property  $ConferenceSid;
 * @property  $CallSidToCoach;
 * @property  $Coaching;
 * @property  $FriendlyName;
 * @property  $AccountSid;
 * @property  $SequenceNumber;
 * @property  $Timestamp;
 * @property  $StatusCallbackEvent;
 * @property  $CallSid;
 * @property  $Muted;
 * @property  $Hold;
 * @property  $EndConferenceOnExit;
 * @property  $StartConferenceOnEnter;
 * @property  $CallSidEndingConference;
 * @property  $ReasonConferenceEnded;
 * @property  $Reason;
 *
 * @property $technical_number
 * @property $friendly_name
 * @property $participant_type_id
 * @property $conference_created_user_id
 * @property $participant_user_id
 * @property $participant_identity
 * @property $call_recording_disabled
 * @property $is_warm_transfer
 * @property int|null $accepted_user_id
 * @property int|null $dep_id
 * @property int|null $old_call_owner_id
 * @property int|null $call_group_id
 *
 * @property $conferenceId
 */
class ConferenceStatusCallbackForm extends Model
{
    public const STATUS_CALL_BACK_EVENTS = [
        'conference-end',
        'conference-start',
        'participant-leave',
        'participant-join',
        'participant-mute',
        'participant-unmute',
        'participant-hold',
        'participant-unhold',
        'participant-speech-start',
        'participant-speech-stop'
    ];

    /** Twilio parameters. Camel Case */

    public $ConferenceSid;
    public $CallSidToCoach;
    public $Coaching;
    public $FriendlyName;
    public $AccountSid;
    public $SequenceNumber;
    public $Timestamp;
    public $StatusCallbackEvent;
    public $CallSid;
    public $Muted;
    public $Hold;
    public $EndConferenceOnExit;
    public $StartConferenceOnEnter;
    public $CallSidEndingConference;
    public $ReasonConferenceEnded;
    public $Reason;

    /** Custom parameters. Snake Case */

    /** number for search Api user for send request */
    public $technical_number;
    public $friendly_name;
    public $participant_type_id;
    public $conference_created_user_id;
    public $participant_user_id;
    public $participant_identity;
    public $call_recording_disabled;
    public $is_warm_transfer;
    public $accepted_user_id;
    public $dep_id;
    public $old_call_owner_id;
    public $call_group_id;

    public $conferenceId;

    public function rules(): array
    {
        return [
            ['ConferenceSid', 'required'],
            ['ConferenceSid', 'string'],
            ['ConferenceSid', 'filter', 'filter' => static function ($value) {
                return mb_substr($value, 0, 34);
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['CallSidToCoach', 'string'],

            ['Coaching', 'default', 'value' => null],
            ['Coaching', 'filter', 'filter' => [$this, 'filterBool'], 'skipOnEmpty' => true],

            ['FriendlyName', 'required'],
            ['FriendlyName', 'string'],

            ['AccountSid', 'required'],
            ['AccountSid', 'string'],

            ['SequenceNumber', 'integer'],
            ['SequenceNumber', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['Timestamp', 'string'],

            ['StatusCallbackEvent', 'string'],
            ['StatusCallbackEvent', 'in', 'range' => self::STATUS_CALL_BACK_EVENTS],

            ['CallSid', 'string'],

            ['Muted', 'default', 'value' => null],
            ['Muted', 'filter', 'filter' => [$this, 'filterBool'], 'skipOnEmpty' => true],

            ['Hold', 'default', 'value' => null],
            ['Hold', 'filter', 'filter' => [$this, 'filterBool'], 'skipOnEmpty' => true],

            ['EndConferenceOnExit', 'default', 'value' => null],
            ['EndConferenceOnExit', 'filter', 'filter' => [$this, 'filterBool'], 'skipOnEmpty' => true],

            ['StartConferenceOnEnter', 'default', 'value' => null],
            ['StartConferenceOnEnter', 'filter', 'filter' => [$this, 'filterBool'], 'skipOnEmpty' => true],

            ['CallSidEndingConference', 'string'],

            ['ReasonConferenceEnded', 'string'],

            ['Reason', 'string'],

            ['technical_number', 'string'],

            ['friendly_name', 'string'],

            ['participant_type_id', 'integer'],
            ['participant_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['conference_created_user_id', 'integer'],
            ['conference_created_user_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['participant_user_id', 'integer'],
            ['participant_user_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['participant_identity', 'string', 'max' => 50],

            ['call_recording_disabled', 'default', 'value' => false],
            ['call_recording_disabled', 'boolean'],

            ['conferenceId', 'integer'],

            ['is_warm_transfer', 'default', 'value' => false],
            ['is_warm_transfer', 'boolean'],

            ['accepted_user_id', 'integer'],

            ['dep_id', 'default', 'value' => null],
            ['dep_id', 'integer'],

            ['old_call_owner_id', 'default', 'value' => null],
            ['old_call_owner_id', 'integer'],

            ['call_group_id', 'integer'],
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

    public function getFormattedTimestamp(): ?string
    {
        return $this->Timestamp ? date('Y-m-d H:i:s', strtotime($this->Timestamp)) : null;
    }
}
