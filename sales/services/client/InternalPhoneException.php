<?php

namespace sales\services\client;

class InternalPhoneException extends \DomainException
{
    public $message = 'Internal phone number.';
}