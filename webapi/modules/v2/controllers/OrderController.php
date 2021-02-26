<?php

namespace webapi\modules\v2\controllers;

use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\forms\api\OrderCreateForm;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderApiManageService;
use sales\repositories\product\ProductQuoteRepository;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\Messages;
use webapi\src\request\RequestBo;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\SourceMessage;
use webapi\src\response\messages\Sources;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\ProxyResponse;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\httpclient\Response;

/**
 * Class OrderController
 *
 * @property RequestBo $requestBo
 * @property OfferRepository $offerRepository
 * @property OrderApiManageService $orderManageService
 * @property ProductQuoteRepository $productQuoteRepository
 */
class OrderController extends BaseController
{
    private $requestBo;
    /**
     * @var OfferRepository
     */
    private OfferRepository $offerRepository;
    /**
     * @var OrderApiManageService
     */
    private OrderApiManageService $orderManageService;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        RequestBo $request,
        OfferRepository $offerRepository,
        OrderApiManageService $orderManageService,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $logger, $config);
        $this->requestBo = $request;
        $this->offerRepository = $offerRepository;
        $this->orderManageService = $orderManageService;
        $this->productQuoteRepository = $productQuoteRepository;
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
     * @api {post} /v2/order/create-proxy Create Order
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
     * @apiErrorExample {json} Error-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": "Failed",
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
    public function actionCreateProxy()
    {
        $data = Yii::$app->request->post();

        try {
            /** @var Response $response */
            if ($this->isClickToBook($data)) {
                $response = $this->requestBo->sendClickToBook($data);
            } else {
                $response = $this->requestBo->sendPhoneToBook($data);
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

        $result->sortUp(Message::STATUS_MESSAGE);
        $result->sortDown(Message::SOURCE_MESSAGE);

        return $result;
    }

    /**
     * @return \webapi\src\response\Response
     *@api {post} /v2/order/create
     * @apiVersion 0.2.0
     * @apiName CreateOrder
     * @apiGroup Order
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     * {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}       offerGid            Offer gid
     * @apiParam {object}       productQuotes         Product Quotes
     * @apiParam {string}       productQuotes.gid     Product Quotes
     * @apiParam {object}       requestData     Request Data for BO
     *
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *      "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
     *      "productQuotes": [
     *          {
     *              "gid": "6fcfc43e977dabffe6a979ebda22a281"
     *          },
     *          {
     *              "gid": "6fcfc43e977dabffe6a979ebdaddfvr2"
     *          }
     *      ],
     *      "requestData": {}
     */
    public function actionCreate(): \webapi\src\response\Response
    {
        $request = Yii::$app->request;
        $form = new OrderCreateForm(count($request->post('productQuotes', [])));

        if (!$form->load($request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        try {
            $offer = $this->offerRepository->findByGid($form->offerGid);

            $order = $this->orderManageService->createOrder((new CreateOrderDTO($offer->of_lead_id)), $form->productQuotes);
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new StatusFailedMessage(),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('order_gid', $order->or_gid),
            )
        );
    }

    private function isClickToBook($data): bool
    {
        return isset($data['FlightRequest']);
    }
}
