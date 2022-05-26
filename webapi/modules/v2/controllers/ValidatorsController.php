<?php

namespace webapi\modules\v2\controllers;

use src\model\validators\phone\PhoneForm;
use src\model\validators\ValidatorsCodeException;
use webapi\src\ApiCodeException;
use webapi\src\Messages;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use Yii;
use webapi\src\response\ErrorResponse;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use yii\helpers\VarDumper;

/**
 * Class ValidatorsController
 *
 */
class ValidatorsController extends BaseController
{
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['phone'] = ['GET'];
        return ['phone' => ['GET']];
    }

    /**
     * @api {post} /v2/validators/phone Validator Phone number
     * @apiVersion 0.2.0
     * @apiName ValidatorPhoneV1
     * @apiGroup Validators
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{50}}           phone                                       Phone number
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *      "phone": "+37369333333"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *               "validation_result": true,
     *       },
     *       "technical": {
     *           "action": "v2/validators/phone",
     *           "response_id": 11930215,
     *           "request_dt": "2019-12-30 12:22:20",
     *           "response_dt": "2019-12-30 12:22:21",
     *           "execution_time": 0.055,
     *           "memory_usage": 1394416
     *       }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "phone": [
     *              "The format of Phone is invalid."
     *           ]
     *       },
     *       "code": 13141,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not loaded data from get request"
     *       ],
     *       "code": 13111,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "phone": [
     *              "Phone cannot be blank."
     *          ]
     *       },
     *       "code": 13141,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     */
    public function actionPhone(): Response
    {
        $form = new PhoneForm();

        if (!$form->load(Yii::$app->request->get())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not loaded data from get request'),
                new CodeMessage(ApiCodeException::GET_DATA_NOT_LOADED)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(ValidatorsCodeException::PHONE_NOT_VALID)
            );
        }

        return new SuccessResponse(
            new DataMessage(
                ['validation_result' => true]
            )
        );
    }
}
