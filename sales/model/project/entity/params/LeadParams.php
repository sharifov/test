<?php

namespace sales\model\project\entity\params;

/**
 * Class LeadParams
 *
 * @property bool $allow_auto_lead_create
 * @property string $default_cid_on_direct_call
 */
class LeadParams
{
    public bool $allow_auto_lead_create;
    public string $default_cid_on_direct_call = '';

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->allow_auto_lead_create = (bool) ($params['allow_auto_lead_create'] ?? self::default()['allow_auto_lead_create']);
        $this->default_cid_on_direct_call = (string)($params['default_cid_on_direct_call'] ?? self::default()['default_cid_on_direct_call']);
    }

    public static function default(): array
    {
        return [
            'allow_auto_lead_create' => true,
            'default_cid_on_direct_call' => ''
        ];
    }
}
