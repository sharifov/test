<?php

namespace sales\model\conference\entity\conferenceEventLog\events;

use common\models\Conference;

class ConferenceStart implements Event
{
    public const NAME = Conference::EVENT_CONFERENCE_START;

    public string $ConferenceSid;
    public string $FriendlyName;
    public string $SequenceNumber;
    public \DateTimeImmutable $Timestamp;
    public string $StatusCallbackEvent;

    public ?int $conferenceId = null;
    public array $raw;

    private function __construct(
        string $ConferenceSid,
        string $FriendlyName,
        string $SequenceNumber,
        string $Timestamp,
        string $StatusCallbackEvent,
        ?int $conferenceId,
        array $raw
    ) {
        $this->ConferenceSid = $ConferenceSid;
        $this->FriendlyName = $FriendlyName;
        $this->SequenceNumber = $SequenceNumber;
        $this->Timestamp = new \DateTimeImmutable($Timestamp);
        $this->StatusCallbackEvent = $StatusCallbackEvent;
        $this->conferenceId = $conferenceId;
        $this->raw = $raw;
    }

    public static function fromArray(array $raw): self
    {
        return new self(
            $raw['ConferenceSid'],
            $raw['FriendlyName'],
            $raw['SequenceNumber'],
            $raw['Timestamp'],
            $raw['StatusCallbackEvent'],
            $raw['conferenceId'] ?? null,
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
            'ConferenceSid' => $this->ConferenceSid,
            'FriendlyName' => $this->FriendlyName,
            'SequenceNumber' => $this->SequenceNumber,
            'Timestamp' => $this->Timestamp->format('Y-m-d H:i:s'),
            'StatusCallbackEvent' => $this->StatusCallbackEvent,
            'conferenceId' => $this->conferenceId,
            'raw' => $this->raw,
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
        return true;
    }

    public function isParticipantHold(): bool
    {
        return false;
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
