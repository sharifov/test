<?php

namespace sales\model\department\department;

/**
 * Class Params
 *
 * @property DefaultPhoneType $defaultPhoneType
 * @property ObjectSettings $object
 * @property bool $callRecordingDisabled
 */
class Params
{
    public DefaultPhoneType $defaultPhoneType;
    public ObjectSettings $object;
    private bool $callRecordingDisabled;

    public function __construct(array $data)
    {
        $this->defaultPhoneType = new DefaultPhoneType($data['default_phone_type']);
        $this->object = new ObjectSettings($data['object']);
        $this->callRecordingDisabled = (bool)($data['call_recording_disabled'] ?? false);
    }

    public function isCallRecordingDisabled(): bool
    {
        return $this->callRecordingDisabled === true;
    }
}
