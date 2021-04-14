<?php

namespace webapi\modules\v2\controllers;

use sales\helpers\app\AppHelper;
use sales\services\TransactionManager;
use webapi\src\forms\payment\PaymentFromBoForm;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use webapi\src\services\payment\PaymentManageApiService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentController
 *
 * @property TransactionManager $transactionManager
 * @property PaymentManageApiService $paymentManageApiService
 */
class PaymentController extends BaseController
{
    private TransactionManager $transactionManager;
    private PaymentManageApiService $paymentManageApiService;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param TransactionManager $transactionManager
     * @param PaymentManageApiService $paymentManageApiService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        TransactionManager $transactionManager,
        PaymentManageApiService $paymentManageApiService,
        $config = []
    ) {
        $this->transactionManager = $transactionManager;
        $this->paymentManageApiService = $paymentManageApiService;

        parent::__construct($id, $module, $logger, $config);
    }

    /**
     * @api {post} /v2/payment/update-bo Create/Update payments from BO
     * @apiVersion 0.1.0
     * @apiName Update payment
     * @apiGroup Payment
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{255}}          fareId                      Fare Id (Order identity)
     * @apiParam {object}               payments                    Payments data array
     * @apiParam {float}                payments.pay_amount         Payment amount
     * @apiParam {string{3}}            payments.pay_currency       Payment currency code (for example USD)
     * @apiParam {date}                 payments.pay_date           Payment date (format Y-m-d)
     * @apiParam {string{1..10}=Capture,Refund,Authorize}    payments.pay_type           Payment Type ("Capture","Refund","Authorize")
     * @apiParam {int}                  payments.pay_auth_id        Payment transaction ID
     * @apiParam {string{100}}          [payments.pay_method_key]   Payment method key (by default "card")
     * @apiParam {string{255}}          [payments.pay_description]  Payment description
     *
     * @apiParam {object}               billingInfo                 Billing Info
     * @apiParam {string{max 30}}       billingInfo.first_name      First Name
     * @apiParam {string{max 30}}       billingInfo.last_name       Last Name
     * @apiParam {string{max 30}}       billingInfo.middle_name     Middle Name
     * @apiParam {string{max 50}}       billingInfo.address         Address
     * @apiParam {string{max 2}}        billingInfo.country_id      Country Id
     * @apiParam {string{max 30}}       billingInfo.city            City
     * @apiParam {string{max 40}}       billingInfo.state           State
     * @apiParam {string{max 10}}       billingInfo.zip             Zip
     * @apiParam {string{max 20}}       billingInfo.phone           Phone
     * @apiParam {string{max 160}}      billingInfo.email           Email
     *
     * @apiParam {object}               creditCard                  Credit Card
     * @apiParam {string{max 50}}       [creditCard.holder_name]    Holder Name
     * @apiParam {string{max 20}}       creditCard.number           Credit Card Number
     * @apiParam {string{max 20}}       [creditCard.type]   Credit Card type (Visa,Master Card,American Express,Discover,Diners Club,JCB)
     * @apiParam {string{max 18}}       creditCard.expiration       Credit Card expiration
     * @apiParam {string{max 4}}        creditCard.cvv              Credit Card cvv
     *
     * @apiParamExample {json} Request-Example:
     *   {
            "fareId": "or6061be5ec5c0e",
            "payments":[
                {
                    "pay_amount": 200.21,
                    "pay_currency": "USD",
                    "pay_auth_id": 728282,
                    "pay_type": "Capture",
                    "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                    "pay_date": "2021-03-25",
                    "pay_method_key":"card",
                    "pay_description": "example description",
                    "creditCard": {
                        "holder_name": "Tester holder",
                        "number": "111**********111",
                        "type": "Visa",
                        "expiration": "07 / 23",
                        "cvv": "123"
                    },
                    "billingInfo": {
                        "first_name": "Hobbit",
                        "middle_name": "Hard",
                        "last_name": "Lover",
                        "address": "1013 Weda Cir",
                        "country_id": "US",
                        "city": "Gotham City",
                        "state": "KY",
                        "zip": "99999",
                        "phone": "+19074861000",
                        "email": "barabara@test.com"
                    }
                },
                {
                    "pay_amount":200.21,
                    "pay_currency":"USD",
                    "pay_auth_id": 728283,
                    "pay_type": "Refund",
                    "pay_code":"xx_XXXXXXXXXXXXXXXXXXXX",
                    "pay_date":"2021-03-25",
                    "pay_method_key":"card",
                    "pay_description": "client is fraud",
                    "creditCard": {
                        "holder_name": "Tester holder",
                        "number": "111**********111",
                        "type": "Visa",
                        "expiration": "07 / 23",
                        "cvv": "321"
                    },
                    "billingInfo": {
                        "first_name": "Eater",
                        "middle_name": "Fresh",
                        "last_name": "Sausage",
                        "address": "1013 Weda Cir",
                        "country_id": "US",
                        "city": "Gotham City",
                        "state": "KY",
                        "zip": "99999",
                        "phone": "+19074861000",
                        "email": "test@test.com"
                    }
                }
            ]
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "resultMessage": "Transaction processed codes(728282,728283)"
     *        },
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
     *        "message": "Payment save is failed. Transaction already exist. Code:(728283)",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (500):
     * HTTP/1.1 500 Internal Server Error
     * {
     *        "status": "Failed",
     *        "source": {
     *            "type": 1,
     *            "status": 500
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": "Failed",
     *        "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
     *        "errors": [
     *              "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
     *        ],
     *        "code": 0,
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionUpdateBo()
    {
        $post = Yii::$app->request->post();
        $paymentFromBoForm = new PaymentFromBoForm();

        if (!$paymentFromBoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$paymentFromBoForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($paymentFromBoForm->getErrors()),
            );
        }

        try {
            $transactionResult = $this->transactionManager->wrap(function () use ($paymentFromBoForm) {
                return $this->paymentManageApiService->handler($paymentFromBoForm);
            });
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'PaymentController:actionCreateBo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Payment save is failed. ' . $throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'resultMessage' => 'Transaction processed codes(' . implode(',', $transactionResult) . ')',
            ])
        );
    }
}
