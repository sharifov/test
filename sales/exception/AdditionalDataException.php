<?php

namespace sales\exception;

use Throwable;

/**
 * Class AdditionalDataException
 */
class AdditionalDataException extends \DomainException
{
    private array $additionalData;

    /**
     * @param array $additionalData
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $additionalData, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->additionalData = $additionalData;
        parent::__construct($message, $code, $previous);
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
