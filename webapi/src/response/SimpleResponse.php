<?php

namespace webapi\src\response;

/**
 * Class SimpleResponse
 *
 * @property array $response
 */
class SimpleResponse extends Response
{
    public const STATUS_CODE_DEFAULT = 200;

    public function getResponse(): array
    {
       return $this->getResponseMessages();
    }

    public function getStatusCodeDefault(): int
    {
        return self::STATUS_CODE_DEFAULT;
    }
}
