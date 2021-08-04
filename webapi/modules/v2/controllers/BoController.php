<?php

namespace webapi\modules\v2\controllers;

use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;

class BoController extends BaseController
{
    /**
     * @api {post} /v2/bo/wh Reprotection Update from BO
     * @apiVersion 0.1.0
     * @apiName ReProtection Update
     * @apiGroup Webhook
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}}           type                        Type of action on reprotection
     * @apiParam {array[]}              data                        Any Data from BO
     * @apiParam {string{10}}           data.booking_id             Booking Id
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "type": "reprotection-update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *              ...
     *      }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *      "technical": {
     *           ...
     *      },
     *      "request": {
     *           ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *      "status": 400,
     *      "message": "Load data error",
     *      "errors": [
     *          "Not found data on POST request"
     *       ],
     *      "technical": {
     *           ...
     *      },
     *      "request": {
     *           ...
     *      }
     * }
     */

    public function actionWh()
    {
        $data = Yii::$app->request->post();

        if ($data) {
            return new SuccessResponse();
        } else {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
    }
}
