<?php

namespace webapi\modules\v2\controllers;

use sales\model\order\OrderCodeException;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use Yii;

/**
 * Class OrderController
 */
class OrderController extends BaseController
{
    public function actionCreate()
    {
        if (!$requestData = Yii::$app->request->post()) {
            return new ErrorResponse([
                'statusCode' => 400,
                'message' => Messages::LOAD_DATA_ERROR,
                'errors' => ['Not found Order data on POST request'],
                'code' => OrderCodeException::API_ORDER_NOT_FOUND_DATA_ON_REQUEST,
            ]);
        }

    }
}
