<?php

namespace webapi\src\response;

/**
 * Class SuccessResponse
 */
class SuccessResponse extends Response
{
    public const STATUS_CODE_SUCCESS = 200;
    public const MESSAGE_SUCCESS = 'OK';

    public function getResponse(): array
    {
        return [
            'status' => $this->getResponseStatusCode(),
            'message' => $this->generateMessage(),
            'data' => $this->data,
        ];
    }

    public function getResponseStatusCode(): int
    {
        return self::STATUS_CODE_SUCCESS;
    }

    private function generateMessage(): string
    {
        return $this->message ?: self::MESSAGE_SUCCESS;
    }
}
