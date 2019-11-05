<?php

namespace frontend\widgets\redial;

/**
 * Class ClientPhonesDTO
 *
 * @property $phone
 * @property $description
 */
class ClientPhonesDTO
{

    public $phone;
    public $description;

    /**
     * @param string $phone
     * @param string|null $description
     */
    public function __construct(string $phone, ?string $description = '')
    {
        $this->phone = $phone;
        $this->description = $description;
    }

}
