<?php

namespace sales\model\project\entity\params;

/**
 * Class LeadParams
 *
 * @property bool $allow_auto_lead_create
 */
class LeadParams
{
    public bool $allow_auto_lead_create;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->allow_auto_lead_create = (bool) ($params['allow_auto_lead_create'] ?? self::default()['allow_auto_lead_create']);
    }

    public static function default(): array
    {
        return [
            'allow_auto_lead_create' => true,
        ];
    }
}
