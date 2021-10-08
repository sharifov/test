<?php

namespace webapi\modules\v1\controllers;

use modules\flight\src\useCases\voluntaryRefundInfo\form\VoluntaryRefundInfoForm;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use sales\helpers\app\AppHelper;
use webapi\src\ApiCodeException;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;

class FlightQuoteRefundController extends ApiBaseController
{
    /**
     * @api {post} /v1/flight-quote-refund/info Voluntary Refund Info
     * @apiVersion 1.0.0
     * @apiName Voluntary Refund Info
     * @apiGroup Voluntary Refund
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{0..10}}    booking_id          Booking ID
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXXXXX"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": {
     *         "productQuoteRefund": {
     *             "id": 3,
     *             "productQuoteId": 774,
     *             "productQuoteGid": "2bd12377691f282e11af12937674e3d1",
     *             "caseId": null,
     *             "caseGid": null,
     *             "orderId": 544,
     *             "orderGid": "2bd12377691f282e11af12937674e3d1",
     *             "statusId": 1,
     *             "statusName": "New",
     *             "sellingPrice": "100.00",
     *             "penaltyAmount": "200.00",
     *             "processingFeeAmount": "200.00",
     *             "refundAmount": "111.00",
     *             "clientCurrency": "USD",
     *             "clientCurrencyRate": "1.00",
     *             "clientSellingPrice": "200.00",
     *             "clientRefundAmount": "400.00",
     *             "createdDt": "2021-07-28 13:52:03",
     *             "updatedDt": "2021-07-28 13:52:03"
     *         }
     *     },
     *     "code": "13200"
     * }
     *
     * @apiErrorExample {json} Error-Response Load Data:
     * HTTP/1.1 200 OK
     *  {
     *     "status": 400,
     *     "message": "Error",
     *     "errors": [
     *         "Load data error"
     *     ],
     *     "code": "13106"
     *  }
     *
     * @apiErrorExample {json} Error-Response Validation:
     * HTTP/1.1 200 OK
     *  {
     *     "status": 422,
     *     "message": "Error",
     *     "errors": [
     *         "ProductQuoteRefund not found by BookingId(ASDAsSF)"
     *     ],
     *     "code": 13112
     *  }
     */
    public function actionInfo()
    {
        if (!$this->request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        try {
            $post = \Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        $voluntaryRefundInfoForm = new VoluntaryRefundInfoForm();
        if (!$voluntaryRefundInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryRefundInfoForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundInfoForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $productQuoteRefund = ProductQuoteRefundQuery::getByBookingId($voluntaryRefundInfoForm->booking_id);
            if (!$productQuoteRefund) {
                throw new \RuntimeException(
                    'ProductQuoteRefund not found by BookingId(' . $voluntaryRefundInfoForm->booking_id . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            return new SuccessResponse(
                new DataMessage([
                    'productQuoteRefund' => $productQuoteRefund->setFields($productQuoteRefund->getApiDataMapped())->toArray(),
                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionInfo:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionInfo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }
}
