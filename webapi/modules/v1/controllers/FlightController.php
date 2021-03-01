<?php

namespace webapi\modules\v1\controllers;

/**
 * Class FlightController
 */
class FlightController extends ApiBaseController
{
    public function actionTicket(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        /* TODO::  */



        $response = [
            'status' => 'success',
            'errors' => []
        ];

        $responseData = $apiLog->endApiLog($response);

        return $responseData;
    }
}
