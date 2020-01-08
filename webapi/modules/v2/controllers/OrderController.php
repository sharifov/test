<?php

namespace webapi\modules\v2\controllers;

use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\request\RequestBo;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\SourceMessage;
use webapi\src\response\messages\Sources;
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\ProxyResponse;
use Yii;
use yii\httpclient\Response;

/**
 * Class OrderController
 *
 * @property RequestBo $request
 */
class OrderController extends BaseController
{
    private $request;

    public function __construct($id, $module, ApiLogger $logger, RequestBo $request, $config = [])
    {
        parent::__construct($id, $module, $logger, $config);
        $this->request = $request;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'filter' => CreditCardFilter::class,
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'filter' => CreditCardFilter::class,
        ];
        return $behaviors;
    }

    /**
     * @api {post} /v2/order/create Create Order
     * @apiVersion 0.2.0
     * @apiName CreateOrder
     * @apiGroup Orders
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": "Success",
     *        "success": {
     *           "recordLocator": "ORZ7I4",
     *           "caseNumber": "OVAGO-282667-TSMITH-AMADEUS-010220-I1B1L1",
     *           "totalPrice": "573.75"
     *        },
     *        "failure": [],
     *        "priceInfo": [],
     *        "errors": [],
     *        "source": {
     *           "type": 1,
     *           "status": 200
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": "Success",
     *        "success": [],
     *        "failure": {
     *              "message": "Price Increase"
     *        },
     *        "priceInfo": {
     *           "totalPrice": 1389.87,
     *           "totalTax": 684.58,
     *           "fareType": "PUB",
     *           "bookingClass": "WWWW",
     *           "currency": "USD",
     *           "detail": {
     *               "ADT": {
     *                   "quantity": 2,
     *               "totalFare": 448.29,
     *               "baseTax": 342.29,
     *               "baseFare": 106,
     *             }
     *           }
     *        },
     *        "errors": [],
     *        "source": {
     *           "type": 1,
     *           "status": 200
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (500):
     *
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
     * @apiErrorExample {json} Error-Response (404):
     *
     * HTTP/1.1 404 Not Found
     * {
     *        "status": "Failed",
     *        "source": {
     *            "type": 1,
     *            "status": 404
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
     *
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
     *
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            /** @var Response $response */
            if ($this->isClickToBook($data)) {
                $response = $this->request->sendClickToBook($data);
            } else {
                $response = $this->request->sendPhoneToBook($data);
            }
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new StatusFailedMessage(),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        try {
            $responseStatusCode = $response->getStatusCode();
        } catch (\Throwable $e) {
            \Yii::error('Cant get status code from response. ' . $e->getMessage(), 'OrderController:create');
            $responseStatusCode = ProxyResponse::STATUS_CODE_DEFAULT;
        }

        $result = new ProxyResponse(
            $response,
            new SourceMessage(Sources::BO, $responseStatusCode)
        );

        return $result;
    }

    private function isClickToBook($data): bool
    {
        return isset($data['FlightRequest']);
    }
}
