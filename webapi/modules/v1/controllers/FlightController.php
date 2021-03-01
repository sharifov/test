<?php

namespace webapi\modules\v1\controllers;

class FlightController extends ApiBaseController
{
    public function actionTicket(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $response = [
            'status' => 'success',
            'errors' => []
        ];

        $responseData = $apiLog->endApiLog($response);

        return $responseData;
    }

    public function actionCancel(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $response = [
            'status' => 'success',
            'errors' => []
        ];

        $responseData = $apiLog->endApiLog($response);

        return $responseData;
    }
}
