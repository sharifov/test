<?php

namespace webapi\src\response;

use yii\base\BaseObject;

/**
 * Class Response
 *
 * @property array $data
 * @property string $message
 */
abstract class Response extends BaseObject
{
    public $data = [];
    public $message;

    public function addData(string $key, array $data): void
    {
        $this->data[$key] = $data;
    }
    public function addDataResponse(DataResponse $response): void
    {
        if ($key = $response->getKey()) {
            $this->data['response'][$key] = $response;
        } else {
            $this->data['response'] = $response;
        }
    }

    abstract public function getResponse(): array;
    abstract public function getResponseStatusCode(): int;
}
