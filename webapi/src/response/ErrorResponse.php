<?php

namespace webapi\src\response;

use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ErrorResponse
 *
 * @property array $errors
 * @property int $code
 */
class ErrorResponse extends Response
{
    public $errors = [];
    public $code = 0;

    public function getResponse()
    {
        throw new UnprocessableEntityHttpException($this->errorsToString(), $this->code);
    }

    public function addData(string $key, array $data): void { }
    public function addDataResponse(DataResponse $response): void { }

    private function errorsToString(): string
    {
        return Json::encode($this->errors);
    }
}
