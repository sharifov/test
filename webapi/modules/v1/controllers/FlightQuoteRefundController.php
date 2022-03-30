<?php

namespace webapi\modules\v1\controllers;

use common\components\BackOffice;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\models\query\FlightRequestQuery;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\api\voluntaryRefundConfirm\VoluntaryRefundConfirmForm;
use modules\flight\src\useCases\api\voluntaryRefundConfirm\VoluntaryRefundConfirmJob;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateJob;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCodeException;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundService;
use modules\flight\src\useCases\voluntaryRefundInfo\form\VoluntaryRefundInfoForm;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\entities\cases\CaseEventLog;
use src\exception\BoResponseException;
use src\helpers\app\AppHelper;
use src\helpers\app\HttpStatusCodeHelper;
use src\helpers\setting\SettingHelper;
use src\services\CurrencyHelper;
use webapi\src\ApiCodeException;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\Messages;
use webapi\src\request\BoRequestDataHelper;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorName;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\TypeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;

/**
 * Class FlightQuoteRefundController
 * @package webapi\modules\v1\controllers
 *
 * @property FlightRequestRepository $flightRequestRepository
 * @property VoluntaryRefundService $voluntaryRefundService
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 */
class FlightQuoteRefundController extends ApiBaseController
{
    private FlightRequestRepository $flightRequestRepository;
    private VoluntaryRefundService $voluntaryRefundService;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;

    public function __construct(
        $id,
        $module,
        FlightRequestRepository $flightRequestRepository,
        VoluntaryRefundService $voluntaryRefundService,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->flightRequestRepository = $flightRequestRepository;
        $this->voluntaryRefundService = $voluntaryRefundService;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
    }

    public function behaviors()
    {
        try {
            \Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            throw new BadRequestHttpException($throwable->getMessage(), ApiCodeException::REQUEST_DATA_INVALID);
        }
        return parent::behaviors();
    }

    /**
     * @api {post} /v1/flight-quote-refund/info Voluntary Refund Info
     * @apiVersion 1.0.0
     * @apiName Flight Voluntary Refund Info
     * @apiGroup Flight Voluntary Refund
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{0..10}}    bookingId          Booking ID
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "bookingId": "XXXXXXX"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     *  {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *           "refund": {
     *               "totalPaid": 300,
     *               "totalAirlinePenalty": 150,
     *               "totalProcessingFee": 30,
     *               "totalRefundable": 150,
     *               "refundCost": 0,
     *               "currency": "USD",
     *               "tickets": [
     *                   {
     *                       "number": "fake-22222",
     *                       "airlinePenalty": 345.47,
     *                       "processingFee": 0,
     *                       "refundable": 128,
     *                       "selling": 473.47,
     *                       "currency": "USD",
     *                       "status": "refunded",
     *                       "refundAllowed": false
     *                   }
     *               ],
     *               "auxiliaryOptions": [
     *                   {
     *                       "type": "auto_check_in",
     *                       "amount": 21.9,
     *                       "amountPerPax": [],
     *                       "refundable": 21.9,
     *                       "details": [],
     *                       "status": "paid",
     *                       "refundAllow": true
     *                   },
     *                   {
     *                       "type": "flexible_ticket",
     *                       "amount": 106.06,
     *                       "amountPerPax": [],
     *                       "refundable": 0,
     *                       "details": [],
     *                       "status": "paid",
     *                       "refundAllow": false
     *                   }
     *               ]
     *           }
     *       },
     *       "code": "13200"
     *   }
     *
     * @apiErrorExample {json} Error-Response Load Data:
     * HTTP/1.1 200 OK
     * {
     *     "status": 400,
     *     "message": "Load data error",
     *     "name": "Client Error: Bad Request",
     *     "code": 13106,
     *     "type": "app",
     *     "errors": []
     * }
     *
     * @apiErrorExample {json} Error-Response Validation:
     * HTTP/1.1 200 OK
     * {
     *   "status": 422,
     *   "message": "Validation error",
     *   "name": "Client Error: Unprocessable Entity",
     *   "errors": {
     *   "bookingId": [
     *          "Booking Id should contain at most 10 characters."
     *      ]
     *   },
     *   "code": 13107,
     *   "type": "app"
     * }
     * @apiErrorExample {html} Codes designation
     * [
     *      13104 - Request is not POST
     *      13106 - Post has not loaded
     *      13107 - Validation Failed
     *      13112 - ProductQuoteRefund not found by BookingId
     * ]
     */
    public function actionInfo()
    {
        if (!$this->request->isPost) {
            throw new MethodNotAllowedHttpException('Method not allowed', ApiCodeException::REQUEST_IS_NOT_POST);
        }

        $post = \Yii::$app->request->post();

        $this->startApiLog($this->action->uniqueId);

        $voluntaryRefundInfoForm = new VoluntaryRefundInfoForm();
        if (!$voluntaryRefundInfoForm->load($post)) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::BAD_REQUEST)),
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage((int)ApiCodeException::POST_DATA_NOT_LOADED),
                new TypeMessage('app')
            ));
        }
        if (!$voluntaryRefundInfoForm->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundInfoForm->getErrors()),
                new CodeMessage((int)ApiCodeException::FAILED_FORM_VALIDATE),
                new TypeMessage('app')
            ));
        }

        try {
            $productQuoteRefund = ProductQuoteRefundQuery::getByBookingId($voluntaryRefundInfoForm->bookingId);
            if (!$productQuoteRefund) {
                throw new \RuntimeException(
                    'ProductQuoteRefund not found by BookingId(' . $voluntaryRefundInfoForm->bookingId . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            $tickets = $productQuoteRefund->productQuoteObjectRefunds;

            $auxiliaryOptions = $productQuoteRefund->productQuoteOptionRefunds;

            return $this->endApiLog(new SuccessResponse(
                new Message('refund', [
                    'totalPaid' => (float)$productQuoteRefund->pqr_client_selling_price,
                    'totalAirlinePenalty' => (float)$productQuoteRefund->pqr_client_penalty_amount,
                    'totalProcessingFee' => (float)$productQuoteRefund->pqr_client_processing_fee_amount,
                    'totalRefundable' => (float)$productQuoteRefund->pqr_client_refund_amount,
                    'refundCost' => (float)$productQuoteRefund->pqr_client_refund_cost,
                    'currency' => $productQuoteRefund->pqr_client_currency,
                    'tickets' => array_map(static function (ProductQuoteObjectRefund $model) {
                        return $model->setFields($model->getApiDataMapped())->toArray();
                    }, $tickets),
                    'auxiliaryOptions' => array_map(static function (ProductQuoteOptionRefund $model) {
                        return $model->setFields($model->getApiDataMapped())->toArray();
                    }, $auxiliaryOptions),
                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            ));
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                AppHelper::throwableLog($throwable),
                'FlightQuoteRefundController:actionInfo:Warning'
            );
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage($throwable->getMessage()),
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)$throwable->getCode()),
                new TypeMessage('app')
            ));
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable, true),
                'FlightQuoteRefundController:actionInfo:Throwable'
            );
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR)),
                new ErrorName('Server Error'),
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new CodeMessage((int)$throwable->getCode()),
                new TypeMessage('app')
            ));
        }
    }

    /**
     * @api {post} /v1/flight-quote-refund/create Flight Voluntary Refund Create
     * @apiVersion 1.0.0
     * @apiName Flight Voluntary Refund Create
     * @apiGroup Flight Voluntary Refund
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{0..50}}        bookingId          Booking ID
     * @apiParam {object}               refund                            Refund Data
     * @apiParam {string{..3}}          refund.currency                   Currency
     * @apiParam {string{..32}}               refund.orderId                    OTA Order Id
     * @apiParam {number}               refund.processingFee              Processing fee
     * @apiParam {number}               refund.penaltyAmount              Airline penalty amount
     * @apiParam {number}               refund.totalRefundAmount          Total refund amount
     * @apiParam {number}               refund.totalPaid                  Total booking amount
     * @apiParam {object}               refund.tickets                    Refund Tickets Array
     * @apiParam {string}               refund.tickets.number             Ticket Number
     * @apiParam {number}               refund.tickets.airlinePenalty     Airline penalty
     * @apiParam {number}               refund.tickets.processingFee      Processing fee
     * @apiParam {number}               refund.tickets.refundable         Refund amount
     * @apiParam {number}               refund.tickets.selling            Selling price
     * @apiParam {string}               refund.tickets.status             Status For BO
     * @apiParam {bool}                 [refund.tickets.refundAllowed]        Refund Allowed
     * @apiParam {object}               refund.auxiliaryOptions             Auxiliary Options Array
     * @apiParam {string}               refund.auxiliaryOptions.type        Auxiliary Options Type
     * @apiParam {number}               refund.auxiliaryOptions.amount      Selling price
     * @apiParam {number}               refund.auxiliaryOptions.refundable  Refundable price
     * @apiParam {string}               refund.auxiliaryOptions.status     Status For BO
     * @apiParam {bool}                 refund.auxiliaryOptions.refundAllow  Refund Allowed
     * @apiParam {object}               [refund.auxiliaryOptions.details]  Details
     * @apiParam {object}               [refund.auxiliaryOptions.amountPerPax]  Amount Per Pax
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
     * @apiParam {string}               billing.country             Country (for example "United States")
     * @apiParam {string{10}}           billing.zip                Zip
     * @apiParam {string{20}}           billing.contact_phone      Contact phone
     * @apiParam {string{160}}          billing.contact_email      Contact email
     * @apiParam {string{60}}           [billing.contact_name]       Contact name
     * @apiParam {object}               payment_request                      Payment request
     * @apiParam {number}               payment_request.amount               Customer must pay for initiate refund process
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string = "card", "stripe"} payment_request.method_key                          Method key (for example "card")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {object}               payment_request.method_data.card     Card (for credit card)
     * @apiParam {string{..20}}           payment_request.method_data.card.number          Number
     * @apiParam {string{..50}}           payment_request.method_data.card.holder_name   Holder name
     * @apiParam {int}                  payment_request.method_data.card.expiration_month       Month
     * @apiParam {int}                  payment_request.method_data.card.expiration_year        Year
     * @apiParam {string{..4}}           payment_request.method_data.card.cvv             CVV
     * @apiParam {object}                payment_request.method_data.stripe                  Stripe (for credit stripe)
     * @apiParam {string}                payment_request.method_data.stripe.token_source            Token Source
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "bookingId": "XXXXXXX",
     *      "refund": {
     *          "orderId": "RET-12321AD",
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
     *                  "refundable": 52.65,
     *                  "selling": 150,
     *                  "status": "issued",
     *                  "refundAllowed": true
     *              }
     *          ],
     *          "auxiliaryOptions": [
     *              {
     *                  "type": "package",
     *                  "amount": 25.00,
     *                  "refundable": 15.00,
     *                  "status": "paid",
     *                  "refundAllow": true,
     *                  "details": {},
     *                  "amountPerPax": {
     *                      "1111111111": 5.45
     *                  }
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
     *          "country": "United States",
     *          "city": "Mayfield",
     *          "state": "KY",
     *          "zip": "99999",
     *          "company_name": "",
     *          "contact_phone": "+19074861000",
     *          "contact_email": "test@test.com",
     *          "contact_name": "Test Name"
     *      },
     *      "payment_request": {
     *          "method_key": "card",
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
     *     "code": "13200",
     *     "saleData": {
     *          "id": 12345,
     *          "bookingId": "P12OJ12"
     *     },
     *     "refund": {
     *         "id": 54321,
     *         "orderId": "RET-12321AD"
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response Load Data:
     * HTTP/1.1 200 OK
     *  {
     *      "status": 400,
     *      "message": "Load data error",
     *      "name": "Client Error: Bad Request",
     *      "code": 13106,
     *      "type": "app",
     *      "errors": []
     *  }
     *
     * @apiErrorExample {json} Error-Response Validation:
     * HTTP/1.1 200 OK
     * {
     *   "status": 422,
     *   "message": "Validation error",
     *   "name": "Client Error: Unprocessable Entity",
     *   "errors": {
     *      "bookingId": [
     *          "Booking Id should contain at most 10 characters."
     *      ]
     *   },
     *   "code": 13107,
     *   "type": "app"
     * }
     * @apiErrorExample {json} Error-Response Error From BO:
     * HTTP/1.1 200 OK
     * {
     *      "status": 422,
     *      "message": "FlightRequest is not found.",
     *      "name": "BO Request Failed",
     *      "code": "15411",
     *      "errors": [],
     *      "type": "app_bo"
     * }
     * @apiErrorExample {html} Codes designation
     * [
     *      13101 - Api User has no related project
     *      13104 - Request is not POST
     *      13106 - Post has not loaded
     *      13107 - Validation Failed
     *      13113 - Flight Request already processing; This feature helps to handle duplicate requests
     *
     *      15401 - Case creation failed; This is system crm error
     *      15402 - Case Sale creation failed; This is system crm error
     *      15403 - Client creation failed; This is system crm error
     *      15404 - Order creation failed; This is system crm error
     *      15405 - Origin Product Quote creation failed; This is system crm error
     *      15409 - Quote not available for refund due to exists active refund or change
     *      15410 - Quote not available for refund due to status of product quote not in changeable list
     *      15411 - Request to BO failed; See tab "Error From BO"
     *      15412 - BO endpoint is not set; This is system crm error
     *      150001 - Flight Request saving failed; This is system crm error
     *
     *      601 - BO Server Error: i.e. request timeout
     *      602 - BO response body is empty
     *      603 - BO response type is invalid (not array)
     * ]
     *
     */
    public function actionCreate()
    {
        if (!$this->request->isPost) {
            throw new MethodNotAllowedHttpException('Method not allowed', ApiCodeException::REQUEST_IS_NOT_POST);
        }

        $this->startApiLog($this->action->uniqueId, true);

        $post = \Yii::$app->request->post();

        if (!$project = $this->apiProject) {
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage('Not found Project with current user: ' . $this->apiUser->au_api_username),
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage('Not found Project with current user: ' . $this->apiUser->au_api_username),
                new CodeMessage((int)ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER),
                new ErrorName('Not found Project'),
                new TypeMessage('app')
            ));
        }

        $voluntaryRefundCreateForm = new VoluntaryRefundCreateForm();
        if (!$voluntaryRefundCreateForm->load($post)) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::BAD_REQUEST)),
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage((int)ApiCodeException::POST_DATA_NOT_LOADED),
                new TypeMessage('app')
            ));
        }

        if (!$voluntaryRefundCreateForm->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundCreateForm->getErrors()),
                new CodeMessage((int)ApiCodeException::FAILED_FORM_VALIDATE),
                new TypeMessage('app')
            ));
        }

        $filteredPost = $voluntaryRefundCreateForm->getFilteredData();
        $hash = FlightRequest::generateHashFromDataJson($filteredPost);
        if (FlightRequestQuery::existActiveRequestByHash($hash)) {
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage('FlightRequest (hash: ' . $hash . ') already processing'),
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)ApiCodeException::REQUEST_ALREADY_PROCESSED),
                new TypeMessage('app')
            ));
        }
        try {
            $flightRequest = FlightRequest::create(
                $voluntaryRefundCreateForm->bookingId,
                FlightRequest::TYPE_VOLUNTARY_REFUND_CREATE,
                $filteredPost,
                $project->id,
                $this->apiUser->au_id
            );
            $flightRequest->statusToPending();
            $flightRequest = $this->flightRequestRepository->save($flightRequest);

            if ($productQuote = ProductQuoteQuery::getProductQuoteByBookingId($voluntaryRefundCreateForm->bookingId)) {
                if ($productQuote->isChangeable()) {
                    if ($productQuote->productQuoteRefundsActive || $productQuote->productQuoteChangesActive) {
                        throw new \DomainException('Quote not available for refund due to exists active refund or change', VoluntaryRefundCodeException::PRODUCT_QUOTE_NOT_AVAILABLE);
                    }
                } else {
                    throw new \DomainException('Quote not available for refund due to status of product quote not in changeable list', VoluntaryRefundCodeException::PRODUCT_QUOTE_NOT_AVAILABLE_CHG_LIST);
                }
                $refundResult = $this->voluntaryRefundService
                    ->processProductQuote($productQuote)
                    ->startRefundAutoProcess($voluntaryRefundCreateForm, $project, $productQuote);
            } else {
                $refundResult = $this->voluntaryRefundService->startRefundAutoProcess($voluntaryRefundCreateForm, $project, null);
            }

            return $this->endApiLog(new SuccessResponse(
                new CodeMessage(ApiCodeException::SUCCESS),
                new Message('saleData', $refundResult->boSaleData),
                new Message('refundData', $refundResult->boRefundData)
            ));
        } catch (BoResponseException $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'FlightQuoteRefundController:actionCreate:BoResponseException');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorName('BO Error'),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)$e->getCode()),
                new TypeMessage('app_bo')
            ));
        } catch (\RuntimeException | \DomainException $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'FlightQuoteRefundController:actionCreate:RuntimeException|DomainException');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)$e->getCode()),
                new TypeMessage('app')
            ));
        } catch (\Throwable $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'FlightQuoteRefundController:actionCreate:Throwable');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR)),
                new ErrorName('Server Error'),
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new CodeMessage((int)$e->getCode()),
                new TypeMessage('app')
            ));
        }
    }

    /**
     * @api {post} /v1/flight-quote-refund/confirm Flight Voluntary Refund Confirm
     * @apiVersion 1.0.0
     * @apiName Flight Voluntary Refund Confirm
     * @apiGroup Flight Voluntary Refund
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{0..10}}        bookingId          Booking ID
     * @apiParam {string{..32}}         refundGid          Refund GID
     * @apiParam {string{..32}}               orderId                    OTA Refund Order Id
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
     * @apiParam {string}               billing.country             Country (for example "United States")
     * @apiParam {string{10}}           billing.zip                Zip
     * @apiParam {string{20}}           billing.contact_phone      Contact phone
     * @apiParam {string{160}}          billing.contact_email      Contact email
     * @apiParam {string{60}}           [billing.contact_name]       Contact name
     * @apiParam {object}               payment_request                      Payment request
     * @apiParam {number}               payment_request.amount               Customer must pay for initiate refund process
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string{50}}            payment_request.method_key           Method key (for example "card")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {string = "card", "stripe"} payment_request.method_key                          Method key (for example "card")
     * @apiParam {string{..20}}           payment_request.method_data.card.number          Number
     * @apiParam {string{..50}}           payment_request.method_data.card.holder_name   Holder name
     * @apiParam {int}                  payment_request.method_data.card.expiration_month       Month
     * @apiParam {int}                  payment_request.method_data.card.expiration_year        Year
     * @apiParam {string{..4}}           payment_request.method_data.card.cvv             CVV
     * @apiParam {object}                payment_request.method_data.stripe                  Stripe (for credit stripe)
     * @apiParam {string}                payment_request.method_data.stripe.token_source            Token Source
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "bookingId": "XXXXXXX",
     *      "refundGid": "6fcb275a1cd60b3a1e93bdda093e383b",
     *      "orderId": "RET-12321AD",
     *      "billing": {
     *          "first_name": "John",
     *          "last_name": "Doe",
     *          "middle_name": "",
     *          "address_line1": "1013 Weda Cir",
     *          "address_line2": "",
     *          "country_id": "US",
     *          "country": "United States",
     *          "city": "Mayfield",
     *          "state": "KY",
     *          "zip": "99999",
     *          "company_name": "",
     *          "contact_phone": "+19074861000",
     *          "contact_email": "test@test.com",
     *          "contact_name": "Test Name"
     *      },
     *      "payment_request": {
     *          "method_key": "card",
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
     *     "code": "13200",
     *     "saleData": {
     *          "id": 12345,
     *          "bookingId": "P12OJ12"
     *     },
     *     "refund": {
     *         "id": 54321,
     *         "orderId": "RET-12321AD"
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response Load Data:
     * HTTP/1.1 200 OK
     *  {
     *      "status": 400,
     *      "message": "Load data error",
     *      "name": "Client Error: Bad Request",
     *      "code": 13106,
     *      "type": "app",
     *      "errors": []
     *  }
     *
     * @apiErrorExample {json} Error-Response Validation:
     * HTTP/1.1 200 OK
     * {
     *   "status": 422,
     *   "message": "Validation error",
     *   "name": "Client Error: Unprocessable Entity",
     *   "errors": {
     *      "bookingId": [
     *          "Booking Id should contain at most 10 characters."
     *      ]
     *   },
     *   "code": 13107,
     *   "type": "app"
     * }
     * @apiErrorExample {json} Error-Response Error From BO:
     * HTTP/1.1 200 OK
     * {
     *      "status": 422,
     *      "message": "FlightRequest is not found.",
     *      "name": "BO Request Failed",
     *      "code": "15411",
     *      "errors": [],
     *      "type": "app_bo"
     * }
     * @apiErrorExample {html} Codes designation
     * [
     *      13101 - Api User has no related project
     *      13104 - Request is not POST
     *      13106 - Post has not loaded
     *      13107 - Validation Failed
     *      13112 - Not found refund in pending status by booking and gid
     *      13113 - Flight Request already processing; This feature helps to handle duplicate requests
     *      15411 - Request to BO failed; See tab "Error From BO"
     *      15412 - BO endpoint is not set; This is system crm error
     *      150001 - Flight Request saving failed; This is system crm error
     *      601 - BO Server Error: i.e. request timeout
     *      602 - BO response body is empty
     *      603 - BO response type is invalid (not array)
     * ]
     */
    public function actionConfirm()
    {
        if (!$this->request->isPost) {
            throw new MethodNotAllowedHttpException('Method not allowed', ApiCodeException::REQUEST_IS_NOT_POST);
        }

        $this->startApiLog($this->action->uniqueId, true);

        $post = \Yii::$app->request->post();

        if (!$project = $this->apiProject) {
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage('Not found Project with current user: ' . $this->apiUser->au_api_username),
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage('Not found Project with current user: ' . $this->apiUser->au_api_username),
                new CodeMessage((int)ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER),
                new ErrorName('Not found Project'),
                new TypeMessage('app')
            ));
        }

        $voluntaryRefundConfirmForm = new VoluntaryRefundConfirmForm();
        if (!$voluntaryRefundConfirmForm->load($post)) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::BAD_REQUEST)),
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage((int)ApiCodeException::POST_DATA_NOT_LOADED),
                new TypeMessage('app')
            ));
        }
        if (!$voluntaryRefundConfirmForm->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryRefundConfirmForm->getErrors()),
                new CodeMessage((int)ApiCodeException::FAILED_FORM_VALIDATE),
                new TypeMessage('app')
            ));
        }

        $filteredPost = $voluntaryRefundConfirmForm->getFilteredData();
        $hash = FlightRequest::generateHashFromDataJson($filteredPost);
        if (FlightRequestQuery::existActiveRequestByHash($hash)) {
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage('FlightRequest (hash: ' . $hash . ') already processing'),
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)ApiCodeException::REQUEST_ALREADY_PROCESSED),
                new TypeMessage('app')
            ));
        }

        try {
            $flightRequest = FlightRequest::create(
                $voluntaryRefundConfirmForm->bookingId,
                FlightRequest::TYPE_VOLUNTARY_REFUND_CONFIRM,
                $filteredPost,
                $project->id,
                $this->apiUser->au_id
            );
            $flightRequest->statusToPending();
            $flightRequest = $this->flightRequestRepository->save($flightRequest);

            $productQuoteRefund = ProductQuoteRefundQuery::getByBookingIdGidStatuses($voluntaryRefundConfirmForm->bookingId, $voluntaryRefundConfirmForm->refundGid, [ProductQuoteRefundStatus::PENDING]);
            if (!$productQuoteRefund) {
                throw new \RuntimeException(
                    'Not found pending product quote refund by bookingId(' . $voluntaryRefundConfirmForm->bookingId . ') and refund gid (' . $voluntaryRefundConfirmForm->refundGid . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            if (!$boRequestEndpoint = SettingHelper::getVoluntaryRefundBoEndpoint()) {
                throw new \RuntimeException('BO endpoint is not set', VoluntaryRefundCodeException::BO_REQUEST_IS_NO_SEND);
            }

            $productQuoteRefund->inProgress();
            $productQuoteRefund->detachBehavior('user');
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            $boDataRequest = BoRequestDataHelper::getDataForVoluntaryRefundConfirm($project->api_key, $voluntaryRefundConfirmForm, $productQuoteRefund);
            $result = BackOffice::voluntaryRefund($boDataRequest, $boRequestEndpoint);

            if (mb_strtolower($result['status']) === 'failed') {
                $productQuoteRefund->error();
                $this->productQuoteRefundRepository->save($productQuoteRefund);

                $flightRequest->statusToError();
                $flightRequest->save();

                return $this->endApiLog(new ErrorResponse(
                    new ErrorName('BO Request Failed'),
                    new MessageMessage($result['message'] ?? 'Unknown message from BO'),
                    new CodeMessage(VoluntaryRefundCodeException::BO_REQUEST_FAILED),
                    new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                    new ErrorsMessage($result['errors'] ?? []),
                    new TypeMessage('app_bo')
                ));
            }

            $job = new VoluntaryRefundConfirmJob(
                $flightRequest->fr_id,
                $productQuoteRefund->pqr_id,
                $voluntaryRefundConfirmForm->orderId
            );
            $jobId = \Yii::$app->queue_job->priority(10)->push($job);

            $flightRequest->fr_job_id = $jobId;
            $this->flightRequestRepository->save($flightRequest);

            $productQuoteRefund->case->addEventLog(
                CaseEventLog::VOLUNTARY_PRODUCT_REFUND_ACCEPTED,
                CaseEventLog::CASE_EVENT_LOG_LIST[CaseEventLog::VOLUNTARY_PRODUCT_REFUND_ACCEPTED],
                ['data_json' => ['product_quote_refund_id' => $productQuoteRefund->pqr_id ?? 0]],
                CaseEventLog::CATEGORY_INFO
            );

            return $this->endApiLog(new SuccessResponse(
                new CodeMessage(ApiCodeException::SUCCESS),
                new Message('saleData', $result['saleData'] ?? []),
                new Message('refundData', $result['refundData'] ?? [])
            ));
        } catch (BoResponseException $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error([
                'message' => $e->getMessage(),
            ], 'FlightQuoteRefundController:actionConfirm:BoResponseException');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorName('BO Error'),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)$e->getCode()),
                new TypeMessage('app')
            ));
        } catch (\RuntimeException | \DomainException $throwable) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::warning([
                'message' => $throwable->getMessage(),
            ], 'FlightQuoteRefundController:actionConfirm:Warning');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage($throwable->getMessage()),
                new ErrorName(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY)),
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new CodeMessage((int)$throwable->getCode()),
                new TypeMessage('app')
            ));
        } catch (\Throwable $throwable) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error([
                'message' => $throwable->getMessage(),
            ], 'FlightQuoteRefundController:actionConfirm:Throwable');
            return $this->endApiLog(new ErrorResponse(
                new MessageMessage(HttpStatusCodeHelper::getName(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR)),
                new ErrorName('Server Error'),
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new CodeMessage((int)$throwable->getCode()),
                new TypeMessage('app')
            ));
        }
    }

    private function endApiLog(Response $response): Response
    {
        $this->apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}
