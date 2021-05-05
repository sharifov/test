<?php

namespace modules\order\src\exceptions;

use Throwable;

/**
 * Class OrderC2BException
 * @package modules\order\src\exceptions
 *
 * @property OrderC2BDtoException $dto
 */
class OrderC2BException extends \DomainException
{
    public $dto;

    public function __construct(OrderC2BDtoException $dto, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->dto = $dto;
        parent::__construct($message, $code, $previous);
    }
}
