<?php

namespace webapi\modules\v2\controllers;

use modules\flight\src\useCases\voluntaryExchange\form\VoluntaryExchangeInfoForm;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeInfoService;
use sales\helpers\app\AppHelper;
use webapi\src\ApiCodeException;
use webapi\src\behaviors\CheckPostBehavior;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteExchangeController
 */
class FlightQuoteExchangeController extends BaseController
{
    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        $config = []
    ) {
        /* TODO::  */
        parent::__construct($id, $module, $logger, $config);
    }

    /**
     * @api {post} /v2/flight-quote-exchange/info Voluntary Exchange Info
     * @apiVersion 0.2.0
     * @apiName Voluntary Exchange Info
     * @apiGroup Voluntary Exchange
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}    booking_id          Booking ID
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXYYYZ"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                    "productQuoteChange": {
                        "id": 950326,
                        "productQuoteId": 950326,
                        "productQuoteGid": "b1ae27497b6eaab24a39fc1370069bd4",
                        "caseId": 35618,
                        "caseGid": "e7dce13b4e6a5f3ccc2cec9c21fa3255",
                        "statusId": 4,
                        "statusName": "Complete",
                        "decisionTypeId": null,
                        "decisionTypeName": "Undefined",
                        "isAutomate": 1,
                        "createdDt": "2021-09-21 03:28:33",
                        "updatedDt": "2021-09-28 09:11:38"
                    }
               },
     *        "code": "13200",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "Load data error",
     *        "errors": [
     *           "Not found data on POST request"
     *        ],
     *        "code": "13106",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": 422,
     *        "message": "Validation error",
     *        "errors": [
     *            "booking_id": [
     *               "booking_id cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionInfo()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        $voluntaryExchangeInfoForm = new VoluntaryExchangeInfoForm();
        if (!$voluntaryExchangeInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryExchangeInfoForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeInfoForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($voluntaryExchangeInfoForm->booking_id, 30);
            if (!$productQuoteChange) {
                throw new \RuntimeException(
                    'ProductQuoteChange not found by BookingId(' . $voluntaryExchangeInfoForm->booking_id . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            return new SuccessResponse(
                new DataMessage([
                    'productQuoteChange' => $productQuoteChange
                        ->setFields(VoluntaryExchangeInfoService::apiDataMapper($productQuoteChange))
                        ->toArray(),
                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }
}
