<?php

namespace webapi\modules\v2\controllers;

class LeadController extends ApiBaseController
{
    public function actionCreate(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);
        $responseData = [
            'status' => 'Success'
        ];
        $responseData = $apiLog->endApiLog($responseData);
        return $responseData;
    }
}
