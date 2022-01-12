<?php

namespace src\exception;

/**
 * Class BoResponseException
 */
class BoResponseException extends \DomainException
{
    public const BO_SERVER_ERROR = 601;
    public const BO_DATA_IS_EMPTY = 602;
    public const BO_RESPONSE_DATA_TYPE_IS_INVALID = 603;
    public const BO_WRONG_ENDPOINT = 604;
}
