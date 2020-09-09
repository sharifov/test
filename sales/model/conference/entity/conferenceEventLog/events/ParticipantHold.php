<?php

namespace sales\model\conference\entity\conferenceEventLog\events;

use common\models\Conference;

class ParticipantHold implements Event
{
    public const NAME = Conference::EVENT_PARTICIPANT_HOLD;

    public string $Coaching;
    public string $FriendlyName;
    public string $SequenceNumber;
    public string $ConferenceSid;
    public string $EndConferenceOnExit;
    public string $CallSid;
    public string $StatusCallbackEvent;
    public \DateTimeImmutable $Timestamp;
    public string $StartConferenceOnEnter;
    public string $Hold;
    public string $Muted;

    public ?int $participant_user_id = null;

    public array $raw;

    private function __construct(
        string $Coaching,
        string $FriendlyName,
        string $SequenceNumber,
        string $ConferenceSid,
        string $EndConferenceOnExit,
        string $CallSid,
        string $StatusCallbackEvent,
        string $Timestamp,
        string $StartConferenceOnEnter,
        string $Hold,
        string $Muted,
        ?string $participant_user_id,
        array $raw
    ) {
        $this->Coaching = $Coaching;
        $this->FriendlyName = $FriendlyName;
        $this->SequenceNumber = $SequenceNumber;
        $this->ConferenceSid = $ConferenceSid;
        $this->EndConferenceOnExit = $EndConferenceOnExit;
        $this->CallSid = $CallSid;
        $this->StatusCallbackEvent = $StatusCallbackEvent;
        $this->Timestamp = new \DateTimeImmutable($Timestamp);
        $this->StartConferenceOnEnter = $StartConferenceOnEnter;
        $this->Hold = $Hold;
        $this->Muted = $Muted;
        $this->participant_user_id = $participant_user_id;
        $this->raw = $raw;
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            $raw['Coaching'],
            $raw['FriendlyName'],
            $raw['SequenceNumber'],
            $raw['ConferenceSid'],
            $raw['EndConferenceOnExit'],
            $raw['CallSid'],
            $raw['StatusCallbackEvent'],
            $raw['Timestamp'],
            $raw['StartConferenceOnEnter'],
            $raw['Hold'],
            $raw['Muted'],
            $raw['participant_user_id'] ?? null,
            $raw
        );
    }

    public static function fromJson(string $json): self
    {
        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return self::fromArray($array);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | 0);
    }

    private function toArray(): array
    {
        return [
            'Coaching' => $this->Coaching,
            'FriendlyName' => $this->FriendlyName,
            'SequenceNumber' => $this->SequenceNumber,
            'ConferenceSid' => $this->ConferenceSid,
            'EndConferenceOnExit' => $this->EndConferenceOnExit,
            'CallSid' => $this->CallSid,
            'StatusCallbackEvent' => $this->StatusCallbackEvent,
            'Timestamp' => $this->Timestamp->format('Y-m-d H:i:s'),
            'StartConferenceOnEnter' => $this->StartConferenceOnEnter,
            'Hold' => $this->Hold,
            'Muted' => $this->Muted,
            'participant_user_id' => $this->participant_user_id,
            'raw' => $this->raw
        ];
    }

    public function isParticipantJoin(): bool
    {
        return false;
    }

    public function isConferenceEnd(): bool
    {
        return false;
    }

    public function isParticipantLeave(): bool
    {
        return false;
    }

    public function isConferenceStart(): bool
    {
        return false;
    }

    public function isParticipantHold(): bool
    {
        return true;
    }

    public function isParticipantUnHold(): bool
    {
        return false;
    }

    public function isParticipantMute(): bool
    {
        return false;
    }

    public function isParticipantUnMute(): bool
    {
        return false;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return clone $this->Timestamp;
    }
}
