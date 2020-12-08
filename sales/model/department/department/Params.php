<?php

namespace sales\model\department\department;

/**
 * Class Params
 *
 * @property DefaultPhoneType $defaultPhoneType
 * @property ObjectSettings $object
 */
class Params
{
    public DefaultPhoneType $defaultPhoneType;
    public ObjectSettings $object;

    public function __construct(array $data)
    {
        $this->defaultPhoneType = new DefaultPhoneType($data['default_phone_type']);
        $this->object = new ObjectSettings($data['object']);
    }
}
