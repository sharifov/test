<?php

namespace src\services\client;

use src\model\client\ClientCodeException;

/**
 * Class InternalEmailException
 * @package src\services\client
 */
class InternalEmailException extends \DomainException
{
    public $message = 'Internal email.';
    public $code = ClientCodeException::INTERNAL_EMAIL;
}
