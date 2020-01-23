<?php

namespace sales\services\client;

use sales\model\client\ClientCodeException;

class InternalPhoneException extends \DomainException
{
    public $message = 'Internal phone number.';
    public $code = ClientCodeException::INTERNAL_PHONE;
}
