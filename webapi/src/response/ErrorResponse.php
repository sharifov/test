<?php

namespace webapi\src\response;

/**
 * Class ErrorResponse
 *
 * @property int $statusCode
 * @property array $errors
 * @property int $code
 */
class ErrorResponse extends Response
{
    public const STATUS_CODE_DEFAULT = 422;
    public const MESSAGE_DEFAULT = 'Error';

    public $statusCode;
    public $errors = [];
    public $code = 0;

    public function getResponse(): array
    {
        $statusCode = $this->statusCode ?: self::STATUS_CODE_DEFAULT;
        $message = $this->message ?: self::MESSAGE_DEFAULT;

        return $this->createResponse($statusCode, $message, $this->errors, $this->code);
    }

    public function getResponseStatusCode(): int
    {
        return $this->statusCode ?: self::STATUS_CODE_DEFAULT;
    }

    private function createResponse($statusCode, $message, $errors, $code): array
    {
        if ($code) {
            $code = (int)$code;
        }
        $response = [
            'status' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
        ];

        if (!empty($this->data)) {
            $response['data'] = $this->data;
        }

        return $response;
    }
}
