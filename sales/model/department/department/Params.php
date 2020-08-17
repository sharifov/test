<?php

namespace sales\model\department\department;

/**
 * Class Params
 *
 * @property DefaultPhoneType $defaultPhoneType
 */
class Params
{
    public DefaultPhoneType $defaultPhoneType;

    public function __construct(array $data)
    {
        $this->defaultPhoneType = new DefaultPhoneType($data['default_phone_type']);
    }
}
