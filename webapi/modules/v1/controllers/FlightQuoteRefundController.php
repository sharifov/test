<?php

namespace webapi\modules\v1\controllers;

use modules\flight\src\useCases\api\voluntaryRefundConfirm\VoluntaryRefundConfirmForm;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;
use modules\flight\src\useCases\voluntaryRefundInfo\form\VoluntaryRefundInfoForm;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
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
     *          "tickets": [
     *              {
     *                  "number": "25346346",
     *                  "airlinePenalty": 47.54,
     *                  "processingFee": 85.65,
     *                  "refundable": 81.25,
     *                  "selling": 200.54,
     *                  "status": "paid"
     *              }
     *          ],
     *          "auxiliaryOptions": [
     *              {
     *                  "type": "package",
     *                  "selling": 45.59,
     *                  "refundAmount": 25.25,
     *                  "status": "paid"
     *              }
     *          ]
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

            $tickets = $productQuoteRefund->productQuoteObjectRefunds;

            $auxiliaryOptions = $productQuoteRefund->productQuoteOptionRefunds;

            return new SuccessResponse(
                new DataMessage([
                //                    'productQuoteRefund' => $productQuoteRefund->setFields($productQuoteRefund->getApiDataMapped())->toArray(),
                    'tickets' => array_map(static function (ProductQuoteObjectRefund $model) {
                        return $model->setFields($model->getApiDataMapped())->toArray();
                    }, $tickets),
                    'auxiliaryOptions' => array_map(static function (ProductQuoteOptionRefund $model) {
                        return $model->setFields($model->getApiDataMapped())->toArray();
                    }, $auxiliaryOptions),
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

    /**
     * @api {post} /v1/flight-quote-refund/create Voluntary Refund Create
     * @apiVersion 1.0.0
     * @apiName Voluntary Refund Create
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
     * @apiParam {string{0..10}}        booking_id          Booking ID
     * @apiParam {object}               refund                            Refund Data
     * @apiParam {string{..3}}          refund.currency                   Currency
     * @apiParam {number}               refund.processingFee              Processing fee
     * @apiParam {number}               refund.penaltyAmount              Airline penalty amount
     * @apiParam {number}               refund.totalRefundAmount          Total refund amount
     * @apiParam {number}               refund.totalPaid                  Total booking amount
     * @apiParam {object}               refund.tickets                    Refund Tickets Array
     * @apiParam {string}               refund.tickets.number             Ticket Number
     * @apiParam {number}               refund.tickets.airlinePenalty     Airline penalty
     * @apiParam {number}               refund.tickets.processingFee      Processing fee
     * @apiParam {number}               refund.tickets.refundAmount       Refund amount
     * @apiParam {number}               refund.tickets.sellingPrice       Selling price
     * @apiParam {string="refunded","request-refund","issued"}              refund.tickets.status             Status
     * @apiParam {object}               refund.auxiliaryOptions             Auxiliary Options Array
     * @apiParam {string}               refund.auxiliaryOptions.type        Auxiliary Options Type
     * @apiParam {number}               refund.auxiliaryOptions.amount      Selling price
     * @apiParam {number}               refund.auxiliaryOptions.refundable  Refundable price
     * @apiParam {string="refunded","used","unpaid"}               refund.auxiliaryOptions.status  Status
     * @apiParam {bool}                 refund.auxiliaryOptions.refundAllow  Refund Allowed
     * @apiParam {object}                 [refund.auxiliaryOptions.details]  Details
     * @apiParam {object}               billing                      Billing
     * @apiParam {string{30}}           billing.first_name           First name
     * @apiParam {string{30}}           billing.last_name            Last name
     * @apiParam {string{30}}           [billing.middle_name]        Middle name
     * @apiParam {string{40}}           [billing.company_name]       Company
     * @apiParam {string{50}}           billing.address_line1        Address line 1
     * @apiParam {string{50}}           [billing.address_line2]      Address line 2
     * @apiParam {string{30}}           billing.city                 City
     * @apiParam {string{40}}           [billing.state]              State
     * @apiParam {string{2}}            billing.country_id           Country code (for example "US")
     * @apiParam {string{10}}           [billing.zip]                Zip
     * @apiParam {string{20}}           [billing.contact_phone]      Contact phone
     * @apiParam {string{160}}          [billing.contact_email]      Contact email
     * @apiParam {string{60}}           [billing.contact_name]       Contact name
     * @apiParam {object}               payment_request                      Payment request
     * @apiParam {number}               payment_request.amount               Customer must pay for initiate refund process
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string{2}}            payment_request.method_key           Method key (for example "cc")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {object}               payment_request.method_data.card     Card (for credit card)
     * @apiParam {string{..20}}           payment_request.method_data.card.number          Number
     * @apiParam {string{..50}}           [payment_request.method_data.card.holder_name]   Holder name
     * @apiParam {int}                  payment_request.method_data.card.expiration_month       Month
     * @apiParam {int}                  payment_request.method_data.card.expiration_year        Year
     * @apiParam {string{..4}}           payment_request.method_data.card.cvv             CVV
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXXXXX",
     *      "refund": {
     *          "processingFee": 12.5,
     *          "penaltyAmount": 100.00,
     *          "totalRefundAmount": 112.5,
     *          "totalPaid": 305.50,
     *          "currency": "USD",
     *          "tickets": [
     *              {
     *                  "number": "465723459",
     *                  "airlinePenalty": 25.36,
     *                  "processingFee": 25,
     *                  "refundAmount": 52.65,
     *                  "sellingPrice": 150,
     *                  "status": "issued"
     *              }
     *          ],
     *          "auxiliaryOptions": [
     *              {
     *                  "type": "package",
     *                  "amount": 25.00,
     *                  "refundable": 15.00,
     *                  "status": "paid",
     *                  "refundAllow": true,
     *                  "details": {}
     *              }
     *          ]
     *      },
     *      "billing": {
     *          "first_name": "John",
     *          "last_name": "Doe",
     *          "middle_name": "",
     *          "address_line1": "1013 Weda Cir",
     *          "address_line2": "",
     *          "country_id": "US",
     *          "city": "Mayfield",
     *          "state": "KY",
     *          "zip": "99999",
     *          "company_name": "",
     *          "contact_phone": "+19074861000",
     *          "contact_email": "test@test.com",
     *          "contact_name": "Test Name"
     *      },
     *      "payment_request": {
     *          "method_key": "cc",
     *          "currency": "USD",
     *          "method_data": {
     *              "card": {
     *                  "number": "4111555577778888",
     *                  "holder_name": "Test test",
     *                  "expiration_month": 10,
     *                  "expiration_year": 23,
     *                  "cvv": "1234"
     *              }
     *          },
     *          "amount": 112.25
     *      }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
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
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "billing.first_name": [
     *             "First Name cannot be blank."
     *         ],
     *         "billing.last_name": [
     *             "Last Name cannot be blank."
     *         ],
     *         "billing.address_line1": [
     *             "Address Line1 cannot be blank."
     *         ],
     *         "billing.city": [
     *             "City cannot be blank."
     *         ],
     *         "billing.country_id": [
     *             "Country Id cannot be blank."
     *         ],
     *         "payment_request.method_key": [
     *             "Method Key cannot be blank."
     *         ],
     *         "payment_request.currency": [
     *             "Currency cannot be blank."
     *         ]
     *     }
     * }
     */
    public function actionCreate()
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

        $voluntaryRefundCreateForm = new VoluntaryRefundCreateForm();
        if (!$voluntaryRefundCreateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryRefundCreateForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundCreateForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            return new SuccessResponse(
//                new DataMessage([
//                    'productQuoteRefund' => $productQuoteRefund->setFields($productQuoteRefund->getApiDataMapped())->toArray(),
//                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionCreate:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionCreate:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v1/flight-quote-refund/confirm Voluntary Refund Confirm
     * @apiVersion 1.0.0
     * @apiName Voluntary Refund Confirm
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
     * @apiParam {string{0..10}}        booking_id          Booking ID
     * @apiParam {object}               billing                      Billing
     * @apiParam {string{30}}           billing.first_name           First name
     * @apiParam {string{30}}           billing.last_name            Last name
     * @apiParam {string{30}}           [billing.middle_name]        Middle name
     * @apiParam {string{40}}           [billing.company_name]       Company
     * @apiParam {string{50}}           billing.address_line1        Address line 1
     * @apiParam {string{50}}           [billing.address_line2]      Address line 2
     * @apiParam {string{30}}           billing.city                 City
     * @apiParam {string{40}}           [billing.state]              State
     * @apiParam {string{2}}            billing.country_id           Country code (for example "US")
     * @apiParam {string{10}}           [billing.zip]                Zip
     * @apiParam {string{20}}           [billing.contact_phone]      Contact phone
     * @apiParam {string{160}}          [billing.contact_email]      Contact email
     * @apiParam {string{60}}           [billing.contact_name]       Contact name
     * @apiParam {object}               payment_request                      Payment request
     * @apiParam {number}               payment_request.amount               Customer must pay for initiate refund process
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string{2}}            payment_request.method_key           Method key (for example "cc")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {object}               payment_request.method_data.card     Card (for credit card)
     * @apiParam {string{..20}}           payment_request.method_data.card.number          Number
     * @apiParam {string{..50}}           [payment_request.method_data.card.holder_name]   Holder name
     * @apiParam {int}                  payment_request.method_data.card.expiration_month       Month
     * @apiParam {int}                  payment_request.method_data.card.expiration_year        Year
     * @apiParam {string{..4}}           payment_request.method_data.card.cvv             CVV
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXXXXX",
     *      "billing": {
     *          "first_name": "John",
     *          "last_name": "Doe",
     *          "middle_name": "",
     *          "address_line1": "1013 Weda Cir",
     *          "address_line2": "",
     *          "country_id": "US",
     *          "city": "Mayfield",
     *          "state": "KY",
     *          "zip": "99999",
     *          "company_name": "",
     *          "contact_phone": "+19074861000",
     *          "contact_email": "test@test.com",
     *          "contact_name": "Test Name"
     *      },
     *      "payment_request": {
     *          "method_key": "cc",
     *          "currency": "USD",
     *          "method_data": {
     *              "card": {
     *                  "number": "4111555577778888",
     *                  "holder_name": "Test test",
     *                  "expiration_month": 10,
     *                  "expiration_year": 23,
     *                  "cvv": "1234"
     *              }
     *          },
     *          "amount": 112.25
     *      }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
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
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "billing.first_name": [
     *             "First Name cannot be blank."
     *         ],
     *         "billing.last_name": [
     *             "Last Name cannot be blank."
     *         ],
     *         "billing.address_line1": [
     *             "Address Line1 cannot be blank."
     *         ],
     *         "billing.city": [
     *             "City cannot be blank."
     *         ],
     *         "billing.country_id": [
     *             "Country Id cannot be blank."
     *         ],
     *         "payment_request.method_key": [
     *             "Method Key cannot be blank."
     *         ],
     *         "payment_request.currency": [
     *             "Currency cannot be blank."
     *         ]
     *     }
     * }
     */
    public function actionConfirm()
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

        $voluntaryRefundConfirmForm = new VoluntaryRefundConfirmForm();
        if (!$voluntaryRefundConfirmForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryRefundConfirmForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundConfirmForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
//            $productQuoteRefund = ProductQuoteRefundQuery::getByBookingId($voluntaryRefundCreateForm->booking_id);
//            if (!$productQuoteRefund) {
//                throw new \RuntimeException(
//                    'ProductQuoteRefund not found by BookingId(' . $voluntaryRefundCreateForm->booking_id . ')',
//                    ApiCodeException::DATA_NOT_FOUND
//                );
//            }

            return new SuccessResponse(
//                new DataMessage([
//                    'productQuoteRefund' => $productQuoteRefund->setFields($productQuoteRefund->getApiDataMapped())->toArray(),
//                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionConfirm:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteRefundController:actionConfirm:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }
}
