<?php

namespace src\services\client;

use src\model\client\ClientCodeException;

class InternalPhoneException extends \DomainException
{
    public $message = 'Internal phone number.';
    public $code = ClientCodeException::INTERNAL_PHONE;
}
