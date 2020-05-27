<?php

namespace sales\services\client;

use sales\model\client\ClientCodeException;

/**
 * Class InternalEmailException
 * @package sales\services\client
 */
class InternalEmailException extends \DomainException
{
    public $message = 'Internal email.';
    public $code = ClientCodeException::INTERNAL_EMAIL;
}
