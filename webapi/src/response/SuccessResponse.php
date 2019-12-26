<?php

namespace webapi\src\response;

/**
 * Class SuccessResponse
 *
 * @property $status
 * @property $message
 * @property $data
 */
class SuccessResponse extends Response
{
    public $status = 200;
    public $message = 'Success';
    public $data = [];

    public function getResponse(): self
    {
        return $this;
    }

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
}
