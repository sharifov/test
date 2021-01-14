<?php

namespace sales\model\project\entity\params;

/**
 * Class SmsParams
 *
 * @property bool $sms_enabled
 */
class SmsParams
{
    public bool $sms_enabled;

    public function __construct(array $params)
    {
        $this->sms_enabled = (bool)($params['sms_enabled'] ?? self::default()['sms_enabled']);
    }

    public static function default(): array
    {
        return [
            'sms_enabled' => false,
        ];
    }

    public function isEnabled(): bool
    {
        return $this->sms_enabled;
    }
}
