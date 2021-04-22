<?php

namespace webapi\modules\v2\controllers;

use common\models\BillingInfo;
use common\models\CreditCard;
use common\models\Payment;
use frontend\helpers\JsonHelper;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\FileSystem;
use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderContact\OrderContactRepository;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataActions;
use modules\order\src\entities\orderData\OrderDataLanguage;
use modules\order\src\entities\orderData\OrderDataMarketCountry;
use modules\order\src\entities\orderData\OrderDataRepository;
use modules\order\src\entities\orderRequest\OrderRequest;
use modules\order\src\entities\orderRequest\OrderRequestRepository;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\exceptions\OrderCodeException;
use modules\order\src\flow\cancelOrder\CanceledException;
use modules\order\src\flow\cancelOrder\CancelOrder;
use modules\order\src\forms\api\cancel\CancelForm;
use modules\order\src\forms\api\create\OrderCreateForm;
use modules\order\src\forms\api\createC2b\OrderCreateC2BForm;
use modules\order\src\forms\api\view\OrderViewForm;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderApiManageService;
use modules\order\src\services\OrderContactManageService;
use modules\order\src\services\OrderDataService;
use modules\product\src\entities\productType\ProductTypeRepository;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\repositories\billingInfo\BillingInfoRepository;
use sales\repositories\creditCard\CreditCardRepository;
use sales\repositories\NotFoundException;
use sales\repositories\product\ProductQuoteRepository;
use sales\repositories\project\ProjectRepository;
use sales\services\TransactionManager;
use webapi\models\ApiUser;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\Messages;
use webapi\src\request\RequestBo;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\DetailErrorMessage;
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
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Response;
use yii\web\NotFoundHttpException;

/**
 * Class OrderController
 *
 * @property RequestBo $requestBo
 * @property OfferRepository $offerRepository
 * @property OrderApiManageService $orderManageService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property OrderRepository $orderRepository
 * @property FileSystem $fileSystem
 * @property OrderRequestRepository $orderRequestRepository
 * @property ProjectRepository $projectRepository
 * @property ProductCreateService $productCreateService
 * @property ProductTypeRepository $productTypeRepository
 * @property TransactionManager $transactionManager
 * @property OrderDataRepository $orderDataRepository
 * @property CreditCardRepository $creditCardRepository
 * @property BillingInfoRepository $billingInfoRepository
 * @property CancelOrder $cancelOrder
 * @property OrderContactRepository $orderContactRepository
 * @property OrderDataService $orderDataService
 * @property OrderContactManageService $orderContactManageService
 */
class OrderController extends BaseController
{
    private $requestBo;
    private OfferRepository $offerRepository;
    private OrderApiManageService $orderManageService;
    private ProductQuoteRepository $productQuoteRepository;
    private OrderRepository $orderRepository;
    private FileSystem $fileSystem;
    private OrderRequestRepository $orderRequestRepository;
    private ProjectRepository $projectRepository;
    private ProductCreateService $productCreateService;
    private ProductTypeRepository $productTypeRepository;
    private TransactionManager $transactionManager;
    private OrderDataRepository $orderDataRepository;
    private CreditCardRepository $creditCardRepository;
    private BillingInfoRepository $billingInfoRepository;
    private CancelOrder $cancelOrder;
    private OrderContactRepository $orderContactRepository;
    private OrderDataService $orderDataService;
    private OrderContactManageService $orderContactManageService;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        RequestBo $request,
        OfferRepository $offerRepository,
        OrderApiManageService $orderManageService,
        ProductQuoteRepository $productQuoteRepository,
        OrderRepository $orderRepository,
        FileSystem $fileSystem,
        OrderRequestRepository $orderRequestRepository,
        ProjectRepository $projectRepository,
        ProductCreateService $productCreateService,
        ProductTypeRepository $productTypeRepository,
        TransactionManager $transactionManager,
        OrderDataRepository $orderDataRepository,
        CreditCardRepository $creditCardRepository,
        BillingInfoRepository $billingInfoRepository,
        CancelOrder $cancelOrder,
        OrderContactRepository $orderContactRepository,
        OrderDataService $orderDataService,
        OrderContactManageService $orderContactManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $logger, $config);
        $this->requestBo = $request;
        $this->offerRepository = $offerRepository;
        $this->orderManageService = $orderManageService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderRepository = $orderRepository;
        $this->fileSystem = $fileSystem;
        $this->orderRequestRepository = $orderRequestRepository;
        $this->projectRepository = $projectRepository;
        $this->productCreateService = $productCreateService;
        $this->productTypeRepository = $productTypeRepository;
        $this->transactionManager = $transactionManager;
        $this->orderDataRepository = $orderDataRepository;
        $this->creditCardRepository = $creditCardRepository;
        $this->billingInfoRepository = $billingInfoRepository;
        $this->cancelOrder = $cancelOrder;
        $this->orderContactRepository = $orderContactRepository;
        $this->orderDataService = $orderDataService;
        $this->orderContactManageService = $orderContactManageService;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'filter' => CreditCardFilter::class,
            'except' => ['get-file'],
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'filter' => CreditCardFilter::class,
            'except' => ['create', 'get-file', 'create-c2b'],
        ];
        $behaviors['responseStatusCode'] = [
            'class' => ResponseStatusCodeBehavior::class,
            'except' => ['get-file'],
        ];
        $behaviors['technical'] = [
            'class' => TechnicalInfoBehavior::class,
            'except' => ['get-file', 'create-c2b'],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['get-file'] = ['GET'];
        return $verbs;
    }

    /**
     * @api {post} /v2/order/create-proxy Create Order Proxy
     * @apiVersion 0.2.0
     * @apiName CreateOrderProxy
     * @apiGroup Order
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
     * @api {post} /v2/order/create Create Order
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
     * @apiParam {string{max 10}}       sourceCid                                           Source cid
     * @apiParam {string{max 32}}       offerGid                                            Offer gid
     * @apiParam {string{max 5}}        languageId                                          Language Id
     * @apiParam {string{max 2}}        marketCountry                                       Market Country
     * @apiParam {Object[]}             productQuotes                                       Product Quotes
     * @apiParam {string{max 32}}       productQuotes.gid                                   Product Quote Gid
     * @apiParam {Object[]}             productQuotes.productOptions                        Quote Options
     * @apiParam {string{max 30}}       productQuotes.productOptions.productOptionKey       Product option key
     * @apiParam {string{max 50}}       [productQuotes.productOptions.name]                   Name
     * @apiParam {string}               [productQuotes.productOptions.description]            Description
     * @apiParam {Decimal}              productQuotes.productOptions.price                  Price
     * @apiParam {string}               productQuotes.productOptions.json_data              Original data
     *
     * @apiParam {Object}               productQuotes.productHolder                         Holder Info
     * @apiParam {string{max 50}}       productQuotes.productHolder.firstName               Holder first name
     * @apiParam {string{max 50}}       productQuotes.productHolder.lastName                Holder last name
     * @apiParam {string{max 50}}       [productQuotes.productHolder.middleName]            Holder middle name
     * @apiParam {string{max 100}}      productQuotes.productHolder.email                   Holder email
     * @apiParam {string{max 20}}       productQuotes.productHolder.phone                   Holder phone
     *
     * @apiParam {Object[]}              [productQuotes.productHolder.data]                   Quote options
     * @apiParam {string}                productQuotes.productHolder.data.segment_uid         Segment uid
     * @apiParam {string}                productQuotes.productHolder.data.pax_uid         Pax uid
     * @apiParam {string}                productQuotes.productHolder.data.trip_uid         Trip uid
     * @apiParam {Decimal}               productQuotes.productHolder.data.total         Total
     * @apiParam {string{max 5}}         productQuotes.productHolder.data.currency         Currency
     * @apiParam {Decimal}               productQuotes.productHolder.data.usd_total         Total price in usd
     * @apiParam {Decimal}               productQuotes.productHolder.data.base_price         Base price in usd
     * @apiParam {Decimal}               productQuotes.productHolder.data.markup_amount         Markup amount
     * @apiParam {Decimal}               productQuotes.productHolder.data.usd_base_price         Base price in usd
     * @apiParam {Decimal}               productQuotes.productHolder.data.usd_markup_amount         Markup amount in usd
     * @apiParam {string{max 255}}       productQuotes.productHolder.data.display_name         Display name
     *
     * @apiParam {Object}               payment                 Payment
     * @apiParam {string}               payment.type            Type
     * @apiParam {string{max 255}}      payment.transactionId   Transaction Id
     * @apiParam {string{format yyyy-mm-dd}}    payment.date            Date
     * @apiParam {Decimal}              payment.amount          Amount
     * @apiParam {string{max 3}}        payment.currency        Currency
     *
     * @apiParam {Object}       [billingInfo]                 BillingInfo
     * @apiParam {string{max 30}}       billingInfo.first_name      First Name
     * @apiParam {string{max 30}}       billingInfo.last_name       Last Name
     * @apiParam {string{max 30}}       billingInfo.middle_name     Middle Name
     * @apiParam {string{max 50}}       billingInfo.address         Address
     * @apiParam {string{max 2}}        billingInfo.country_id      Country Id
     * @apiParam {string{max 30}}       billingInfo.city            City
     * @apiParam {string{max 40}}       billingInfo.state           State
     * @apiParam {string{max 10}}       billingInfo.zip             Zip
     * @apiParam {string{max 20}}       billingInfo.phone           Phone <code>Deprecated</code>
     * @apiParam {string{max 160}}      billingInfo.email           Email <code>Deprecated</code>
     *
     * @apiParam {Object}               creditCard                  Credit Card
     * @apiParam {string{max 50}}       [creditCard.holder_name]      Holder Name
     * @apiParam {string{max 20}}       creditCard.number           Credit Card Number
     * @apiParam {string}               [creditCard.type]             Credit Card type
     * @apiParam {string{max 18}}       creditCard.expiration       Credit Card expiration
     * @apiParam {string{max 4}}        creditCard.cvv              Credit Card cvv
     *
     * @apiParam {Object}               [Tips]                  Tips
     * @apiParam {Decimal}              Tips.total_amount       Total Amount
     *
     * @apiParam {Object}                       Paxes[]                 Paxes
     * @apiParam {string}                       Paxes.uid                   Uid
     * @apiParam {string{max 40}}               [Paxes.first_name]            First Name
     * @apiParam {string{max 40}}               [Paxes.last_name]             Last Name
     * @apiParam {string{max 40}}               [Paxes.middle_name]           Middle Name
     * @apiParam {string{max 5}}    [Paxes.nationality]           Nationality
     * @apiParam {string{max 1}}                [Paxes.gender]                Gender
     * @apiParam {string{format yyyy-mm-dd}}    [Paxes.birth_date]            Birth Date
     * @apiParam {string{max 100}}              [Paxes.email]                 Email
     * @apiParam {string{max 5}}                [Paxes.language]              Language
     * @apiParam {string{max 5}}                [Paxes.citizenship]           Citizenship
     *
     *
     * @apiParam {Object[]}             contactsInfo                 BillingInfo
     * @apiParam {string{max 50}}       contactsInfo.first_name      First Name
     * @apiParam {string{max 50}}       [contactsInfo.last_name]       Last Name
     * @apiParam {string{max 50}}       [contactsInfo.middle_name]     Middle Name
     * @apiParam {string{max 20}}       [contactsInfo.phone]           Phone number
     * @apiParam {string{max 100}}      contactsInfo.email           Email
     *
     * @apiParam {Object}       Request                 Request Data for BO
     *
     * @apiParamExample {json} Request-Example:
     * {
    "sourceCid": "OVA102",
    "offerGid": "73c8bf13111feff52794883446461740",
    "languageId": "en-US",
    "marketCountry": "US",
    "productQuotes": [
        {
            "gid": "aebf921f5a64a7ac98d4942ace67e498",
            "productOptions": [
                {
                    "productOptionKey": "travelGuard",
                    "name": "Travel Guard",
                    "description": "",
                    "price": 20,
                    "json_data": "",
                    "data": [
                        {
                            "segment_uid": "fqs604635abf02ae",
                            "pax_uid": "fp604635abe9c6a",
                            "trip_uid": "fqt604635abed0e0",
                            "total": 2.00,
                            "currency": "USD",
                            "usd_total": 2.00,
                            "base_price": 2.00,
                            "markup_amount": 0,
                            "usd_base_price": 2.00,
                            "usd_markup_amount": 0,
                            "display_name": "Seat: 18E, CQ 7602"
                        }
                    ]

                }
            ],
            "productHolder": {
                "firstName": "Test",
                "lastName": "Test",
                "middleName": "",
                "email": "test@test.test",
                "phone": "+19074861000"
            }
        },
        {
            "gid": "6fcfc43e977dabffe6a979ebdaddfvr2",
            "productHolder": {
                "firstName": "Test 2",
                "lastName": "Test 2",
                "email": "test2@test.test",
                "phone": "+19074861002"
            }
        }
    ],
    "payment": {
        "type": "card",
        "transactionId": 1234567890,
        "date": "2021-03-20",
        "amount": 821.49,
        "currency": "USD"
    },
    "billingInfo": {
        "first_name": "Barbara Elmore",
        "middle_name": "",
        "last_name": "T",
        "address": "1013 Weda Cir",
        "country_id": "US",
        "city": "Mayfield",
        "state": "KY",
        "zip": "99999",
        "phone": "+19074861000",
        "email": "mike.kane@techork.com"
    },
    "creditCard": {
        "holder_name": "Barbara Elmore",
        "number": "1111111111111111",
        "type": "Visa",
        "expiration": "07 / 23",
        "cvv": "324"
    },
    "tips": {
        "total_amount": 20
    },
    "paxes": [
        {
            "uid": "fp6047195e67b7a",
            "first_name": "Test name",
            "last_name": "Test last name",
            "middle_name": "Test middle name",
            "nationality": "US",
            "gender": "M",
            "birth_date": "1963-04-07",
            "email": "mike.kane@techork.com",
            "language": "en-US",
            "citizenship": "US"
        }
    ],
    "contactsInfo": [
        {
            "first_name": "Barbara",
            "last_name": "Elmore",
            "middle_name": "",
            "phone": "+19074861000",
            "email": "barabara@test.com"
        },
        {
            "first_name": "John",
            "last_name": "Doe",
            "middle_name": "",
            "phone": "+19074865678",
            "email": "john@test.com"
        }
    ],
    "Request": {
        "offerGid": "85a06c376a083f47e56b286b1265c160",
        "offerUid": "of60264c1484090",
        "apiKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
        "source": "I1B1L1",
        "subSource": "-",
        "totalOrderAmount": 821.49,
        "FlightRequest": {
            "productGid": "c6ae37ae73380c773cadf28fc0af9db2",
            "uid": "OE96040",
            "email": "mike.kane@techork.com",
            "marker": null,
            "client_ip_address": "92.115.180.30",
            "trip_protection_amount": "0",
            "insurance_code": "P7",
            "is_facilitate": 0,
            "delay_change": false,
            "is_b2b": false,
            "uplift": false,
            "alipay": false,
            "user_country": "us",
            "user_language": "en-US",
            "user_time_format": "h:mm a",
            "user_month_date_format": {
                "long": "EEE MMM d",
                "short": "MMM d",
                "fullDateLong": "EEE MMM d",
                "fullDateShort": "MMM d, YYYY"
            },
            "currency_symbol": "$",
            "pnr": null
        },
        "HotelRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "DriverRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "AttractionRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "CruiseRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "Card": {
            "user_id": null,
            "nickname": "B****** E***** T",
            "number": "************6444",
            "type": "Visa",
            "expiration_date": "07 / 2023",
            "first_name": "Barbara Elmore",
            "middle_name": "",
            "last_name": "T",
            "address": "1013 Weda Cir",
            "country_id": "US",
            "city": "Mayfield",
            "state": "KY",
            "zip": "99999",
            "phone": "+19074861000",
            "deleted": null,
            "cvv": "***",
            "auth_attempts": null,
            "country": "United States",
            "calling": "",
            "client_ip_address": "92.115.180.30",
            "email": "mike.kane@techork.com",
            "document": null
        },
        "AirRouting": {
            "results": [
                {
                    "gds": "S",
                    "key": "2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=",
                    "pcc": "8KI0",
                    "cons": "GTT",
                    "keys": {
                        "services": {
                            "support": {
                                "amount": 75
                            }
                        },
                        "seatHoldSeg": {
                            "trip": 0,
                            "seats": 9,
                            "segment": 0
                        },
                        "verification": {
                            "headers": {
                                "X-Client-Ip": "92.115.180.30",
                                "X-Kiv-Cust-Ip": "92.115.180.30",
                                "X-Kiv-Cust-ipv": "0",
                                "X-Kiv-Cust-ssid": "ovago-dev-0484692",
                                "X-Kiv-Cust-direct": "true",
                                "X-Kiv-Cust-browser": "desktop"
                            }
                        }
                    },
                    "meta": {
                        "eip": 0,
                        "bags": 2,
                        "best": false,
                        "lang": "en",
                        "rank": 6,
                        "group1": "LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75",
                        "country": "us",
                        "fastest": false,
                        "noavail": false,
                        "cheapest": true,
                        "searchId": "T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw"
                    },
                    "cabin": "Y",
                    "trips": [
                        {
                            "tripId": 1,
                            "duration": 1150,
                            "segments": [
                                {
                                    "meal": "D",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 7305,
                                    "duration": 870,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 1,
                                    "arrivalTime": "2021-05-15 04:00",
                                    "airEquipType": "773",
                                    "bookingClass": "U",
                                    "flightNumber": "103",
                                    "departureTime": "2021-05-13 22:30",
                                    "marriageGroup": "O",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "MNL",
                                    "departureAirportCode": "LAX",
                                    "arrivalAirportTerminal": "2",
                                    "departureAirportTerminal": "B"
                                },
                                {
                                    "meal": "B",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 728,
                                    "duration": 130,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 2,
                                    "arrivalTime": "2021-05-15 08:40",
                                    "airEquipType": "321",
                                    "bookingClass": "U",
                                    "flightNumber": "890",
                                    "departureTime": "2021-05-15 06:30",
                                    "marriageGroup": "I",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "TPE",
                                    "departureAirportCode": "MNL",
                                    "arrivalAirportTerminal": "1",
                                    "departureAirportTerminal": "1"
                                }
                            ]
                        },
                        {
                            "tripId": 2,
                            "duration": 1490,
                            "segments": [
                                {
                                    "meal": "H",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 728,
                                    "duration": 145,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 1,
                                    "arrivalTime": "2021-06-20 12:05",
                                    "airEquipType": "321",
                                    "bookingClass": "U",
                                    "flightNumber": "891",
                                    "departureTime": "2021-06-20 09:40",
                                    "marriageGroup": "O",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "MNL",
                                    "departureAirportCode": "TPE",
                                    "arrivalAirportTerminal": "2",
                                    "departureAirportTerminal": "1"
                                },
                                {
                                    "meal": "D",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 7305,
                                    "duration": 805,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 2,
                                    "arrivalTime": "2021-06-20 19:30",
                                    "airEquipType": "773",
                                    "bookingClass": "U",
                                    "flightNumber": "102",
                                    "departureTime": "2021-06-20 21:05",
                                    "marriageGroup": "I",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "LAX",
                                    "departureAirportCode": "MNL",
                                    "arrivalAirportTerminal": "B",
                                    "departureAirportTerminal": "1"
                                }
                            ]
                        }
                    ],
                    "paxCnt": 1,
                    "prices": {
                        "comm": 0,
                        "isCk": false,
                        "ccCap": 16.900002,
                        "markup": 50,
                        "oMarkup": {
                            "amount": 50,
                            "currency": "USD"
                        },
                        "markupId": 8833,
                        "totalTax": 321.75,
                        "markupUid": "1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e",
                        "totalPrice": 767.75,
                        "lastTicketDate": "2021-03-31"
                    },
                    "currency": "USD",
                    "fareType": "SR",
                    "maxSeats": 9,
                    "tripType": "RT",
                    "penalties": {
                        "list": [
                            {
                                "type": "re",
                                "permitted": false,
                                "applicability": "before"
                            },
                            {
                                "type": "re",
                                "permitted": false,
                                "applicability": "after"
                            },
                            {
                                "type": "ex",
                                "amount": 425,
                                "oAmount": {
                                    "amount": 425,
                                    "currency": "USD"
                                },
                                "permitted": true,
                                "applicability": "before"
                            },
                            {
                                "type": "ex",
                                "amount": 425,
                                "oAmount": {
                                    "amount": 425,
                                    "currency": "USD"
                                },
                                "permitted": true,
                                "applicability": "after"
                            }
                        ],
                        "refund": false,
                        "exchange": true
                    },
                    "routingId": 1,
                    "currencies": [
                        "USD"
                    ],
                    "founded_dt": "2021-02-25 13:44:54.570",
                    "passengers": {
                        "ADT": {
                            "cnt": 1,
                            "tax": 321.75,
                            "comm": 0,
                            "ccCap": 16.900002,
                            "price": 767.75,
                            "codeAs": "JCB",
                            "markup": 50,
                            "occCap": {
                                "amount": 16.900002,
                                "currency": "USD"
                            },
                            "baseTax": 271.75,
                            "oMarkup": {
                                "amount": 50,
                                "currency": "USD"
                            },
                            "baseFare": 446,
                            "oBaseTax": {
                                "amount": 271.75,
                                "currency": "USD"
                            },
                            "oBaseFare": {
                                "amount": 446,
                                "currency": "USD"
                            },
                            "pubBaseFare": 446
                        }
                    },
                    "ngsFeatures": {
                        "list": null,
                        "name": "",
                        "stars": 3
                    },
                    "currencyRates": {
                        "CADUSD": {
                            "to": "USD",
                            "from": "CAD",
                            "rate": 0.78417
                        },
                        "DKKUSD": {
                            "to": "USD",
                            "from": "DKK",
                            "rate": 0.16459
                        },
                        "EURUSD": {
                            "to": "USD",
                            "from": "EUR",
                            "rate": 1.23967
                        },
                        "GBPUSD": {
                            "to": "USD",
                            "from": "GBP",
                            "rate": 1.37643
                        },
                        "KRWUSD": {
                            "to": "USD",
                            "from": "KRW",
                            "rate": 0.00091
                        },
                        "MYRUSD": {
                            "to": "USD",
                            "from": "MYR",
                            "rate": 0.25006
                        },
                        "SEKUSD": {
                            "to": "USD",
                            "from": "SEK",
                            "rate": 0.12221
                        },
                        "TWDUSD": {
                            "to": "USD",
                            "from": "TWD",
                            "rate": 0.03592
                        },
                        "USDCAD": {
                            "to": "CAD",
                            "from": "USD",
                            "rate": 1.30086
                        },
                        "USDDKK": {
                            "to": "DKK",
                            "from": "USD",
                            "rate": 6.19797
                        },
                        "USDEUR": {
                            "to": "EUR",
                            "from": "USD",
                            "rate": 0.83926
                        },
                        "USDGBP": {
                            "to": "GBP",
                            "from": "USD",
                            "rate": 0.75587
                        },
                        "USDKRW": {
                            "to": "KRW",
                            "from": "USD",
                            "rate": 1117.1008
                        },
                        "USDMYR": {
                            "to": "MYR",
                            "from": "USD",
                            "rate": 4.07943
                        },
                        "USDSEK": {
                            "to": "SEK",
                            "from": "USD",
                            "rate": 8.34736
                        },
                        "USDTWD": {
                            "to": "TWD",
                            "from": "USD",
                            "rate": 28.96525
                        },
                        "USDUSD": {
                            "to": "USD",
                            "from": "USD",
                            "rate": 1
                        }
                    },
                    "validatingCarrier": "PR"
                }
            ],
            "additionalInfo": {
                "cabin": {
                    "C": "Business",
                    "F": "First",
                    "J": "Premium Business",
                    "P": "Premium First",
                    "S": "Premium Economy",
                    "Y": "Economy"
                },
                "airline": {
                    "PR": {
                        "name": "Philippine Airlines"
                    }
                },
                "airport": {
                    "LAX": {
                        "city": "Los Angeles",
                        "name": "Los Angeles International Airport",
                        "country": "United States"
                    },
                    "MNL": {
                        "city": "Manila",
                        "name": "Ninoy Aquino International Airport",
                        "country": "Philippines"
                    },
                    "TPE": {
                        "city": "Taipei",
                        "name": "Taiwan Taoyuan International Airport",
                        "country": "Taiwan"
                    }
                },
                "general": {
                    "tripType": "rt"
                }
            }
        },
        "Passengers": {
            "Flight": [
                {
                    "id": null,
                    "user_id": null,
                    "first_name": "Arthur",
                    "middle_name": "",
                    "last_name": "Davis",
                    "birth_date": "1963-04-07",
                    "gender": "M",
                    "seats": null,
                    "assistance": null,
                    "nationality": "US",
                    "passport_id": null,
                    "passport_valid_date": null,
                    "email": null,
                    "codeAs": null
                }
            ],
            "Hotel": [
                {
                    "first_name": "mike",
                    "last_name": "kane"
                }
            ],
            "Driver": [
                {
                    "first_name": "mike",
                    "last_name": "kane",
                    "age": "30-69",
                    "birth_date": "1973-04-07"
                }
            ],
            "Attraction": [
                {
                    "first_name": "mike",
                    "last_name": "kane",
                    "language_service": "US"
                }
            ],
            "Cruise": [
                {
                    "first_name": "Arthur",
                    "last_name": "Davis",
                    "citizenship": "US",
                    "birth_date": "1963-04-07",
                    "gender": "M"
                }
            ]
        },
        "Insurance": {
            "total_amount": "20",
            "record_id": "396393",
            "passengers": [
                {
                    "nameRef": "0",
                    "amount": 20
                }
            ]
        },
        "Tip": {
            "total_amount": 20
        },
        "AuxiliarProducts": {
            "Flight": {
                "basket": {
                    "1c3df555-a2dc-4813-a055-2a8bf56fd8f1": {
                        "basket_item_id": "1c3df555-a2dc-4813-a055-2a8bf56fd8f1",
                        "benefits": [],
                        "display_name": "10kg Bag",
                        "price": {
                            "base": {
                                "amount": 2000,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 1820,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            },
                            "fees": [],
                            "markups": [
                                {
                                    "amount": 600,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 546,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "markup_type": "markup"
                                }
                            ],
                            "taxes": [
                                {
                                    "amount": 200,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 182,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "tax_type": "tax"
                                }
                            ],
                            "total": {
                                "amount": 2400,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 2184,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            }
                        },
                        "product_details": {
                            "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
                            "passenger_id": "p1",
                            "size": 150,
                            "size_unit": "cm",
                            "weight": 10,
                            "weight_unit": "kg"
                        },
                        "product_id": "741bcc97-c2fe-4820-b14d-f11f32e6fadb",
                        "product_type": "bag",
                        "quantity": 1,
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
                        "validity": {
                            "state": "valid",
                            "valid_from": "2020-05-22T16:34:08Z",
                            "valid_to": "2020-05-22T16:49:08Z"
                        }
                    },
                    "2654f3f9-8990-4d2e-bdea-3b341ad5d1de": {
                        "basket_item_id": "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
                        "benefits": [],
                        "display_name": "Seat 15C",
                        "price": {
                            "base": {
                                "amount": 2000,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 1820,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            },
                            "fees": [],
                            "markups": [
                                {
                                    "amount": 400,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 364,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "markup_type": "markup"
                                }
                            ],
                            "taxes": [
                                {
                                    "amount": 200,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": [],
                                    "tax_type": "tax"
                                }
                            ],
                            "total": {
                                "amount": 2600,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 2366,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            }
                        },
                        "product_details": {
                            "column": "C",
                            "passenger_id": "p1",
                            "row": 15,
                            "segment_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
                        },
                        "product_id": "a17e10ca-0c9a-4691-9922-d664a3b52382",
                        "product_type": "seat",
                        "quantity": 1,
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
                        "validity": {
                            "state": "valid",
                            "valid_from": "2020-05-22T16:34:08Z",
                            "valid_to": "2020-05-22T16:49:08Z"
                        }
                    },
                    "5d5e1bce-4577-4118-abcb-155823d8b4a3": [],
                    "6acd57ba-ccb7-4e86-85e7-b3e586caeae2": [],
                    "dffac4ba-73b9-4b1b-9334-001817fff0cf": [],
                    "e960eff9-7628-4645-99d8-20a6e22f6419": []
                },
                "country": "US",
                "currency": "USD",
                "journeys": [
                    {
                        "journey_id": "aab8980e-b263-4624-ad40-d6e5e364b4e9",
                        "segments": [
                            {
                                "arrival_airport": "LHR",
                                "arrival_time": "2020-07-07T22:30:00Z",
                                "departure_airport": "EDI",
                                "departure_time": "2020-07-07T21:10:00Z",
                                "fare_basis": "OTZ0RO/Y",
                                "fare_class": "O",
                                "fare_family": "Basic Economy",
                                "marketing_airline": "BA",
                                "marketing_flight_number": "1465",
                                "number_of_stops": 0,
                                "operating_airline": "BA",
                                "operating_flight_number": "1465",
                                "segment_id": "938d8e82-dd7c-4d85-8ab4-38fea8753f6f"
                            }
                        ]
                    },
                    {
                        "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
                        "segments": [
                            {
                                "arrival_airport": "EDI",
                                "arrival_time": "2020-07-14T08:35:00Z",
                                "departure_airport": "LGW",
                                "departure_time": "2020-07-14T07:05:00Z",
                                "fare_basis": "NALZ0KO/Y",
                                "fare_class": "N",
                                "fare_family": "Basic Economy",
                                "marketing_airline": "BA",
                                "marketing_flight_number": "2500",
                                "number_of_stops": 0,
                                "operating_airline": "BA",
                                "operating_flight_number": "2500",
                                "segment_id": "7d693cb0-d6d8-49f0-9489-866b3d789215"
                            }
                        ]
                    }
                ],
                "language": "en-US",
                "orders": [],
                "passengers": [
                    {
                        "first_names": "Vincent Willem",
                        "passenger_id": "ee850c82-e150-4f35-b0c7-228064c2964b",
                        "surname": "Van Gogh"
                    }
                ],
                "tickets": [
                    {
                        "basket_item_ids": [
                            "dffac4ba-73b9-4b1b-9334-001817fff0cf",
                            "e960eff9-7628-4645-99d8-20a6e22f6419",
                            "6acd57ba-ccb7-4e86-85e7-b3e586caeae2",
                            "5d5e1bce-4577-4118-abcb-155823d8b4a3"
                        ],
                        "journey_ids": [
                            "aab8980e-b263-4624-ad40-d6e5e364b4e9"
                        ],
                        "state": "in_basket",
                        "ticket_basket_item_id": "dffac4ba-73b9-4b1b-9334-001817fff0cf",
                        "ticket_id": "8c1c9fc8-d968-4733-93a8-6067bac2543f"
                    },
                    {
                        "basket_item_ids": [
                            "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
                            "1c3df555-a2dc-4813-a055-2a8bf56fd8f1"
                        ],
                        "journey_ids": [
                            "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
                        ],
                        "offered_price": {
                            "currency": "USD",
                            "decimal_places": 2,
                            "total": 20000
                        },
                        "state": "offered",
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252"
                    }
                ],
                "trip_access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
                "trip_id": "23259b86-3208-44c9-85cc-4b116a822bff",
                "trip_state_hash": "69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee"
            },
            "Hotel": [],
            "Driver": [],
            "Attraction": [],
            "Cruise": []
        },
        "Payment": {
            "type": "CARD",
            "transaction_id": "1234567890",
            "card_id": 234567,
            "auth_id": 123456
        }
    }
}
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     * "status": 200,
     * "message": "OK",
     * "data": {
     * "order_gid": "ef75bfa7cc60af154c22c43e3732350f"
     * },
     * "technical": {
     * "action": "v2/order/create",
     * "response_id": 327,
     * "request_dt": "2021-02-27 08:49:46",
     * "response_dt": "2021-02-27 08:49:46",
     * "execution_time": 0.094,
     * "memory_usage": 1356920
     * }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     * "status": 422,
     * "message": "Validation error",
     * "errors": {
     * "payment.type": [
     * "Type is invalid."
     * ]
     * },
     * "code": 0,
     * "technical": {
     * "action": "v2/order/create",
     * "response_id": 328,
     * "request_dt": "2021-02-27 08:52:06",
     * "response_dt": "2021-02-27 08:52:06",
     * "execution_time": 0.021,
     * "memory_usage": 437656
     * }
     * }
     *
     */
    public function actionCreate(): \webapi\src\response\Response
    {
        $request = Yii::$app->request;
        $form = new OrderCreateForm();

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
            $orderRequest = OrderRequest::create($request->post(), OrderSourceType::P2B);
            $this->orderRequestRepository->save($orderRequest);

            $offer = $this->offerRepository->findByGid($form->offerGid);

            $dto = new CreateOrderDTO(
                $offer->of_lead_id,
                $form->payment->currency,
                $request->post(),
                OrderSourceType::P2B,
                $orderRequest->orr_id,
                $form->projectId,
                OrderStatus::PENDING,
                null,
                $form->languageId,
                $form->marketCountry
            );
            $order = $this->orderManageService->createOrder($dto, $form, null);

            $response = new SuccessResponse(
                new DataMessage(
                    new Message('order_gid', $order->or_gid),
                )
            );

            $orderRequest->successResponse(ArrayHelper::toArray($response));
            $this->orderRequestRepository->save($orderRequest);
            return $response;
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'API::OrderController::actionCreate::Throwable');

            $response = new ErrorResponse(
                new StatusFailedMessage(),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );

            if (isset($orderRequest)) {
                $orderRequest->errorResponse(ArrayHelper::toArray($response));
                $this->orderRequestRepository->save($orderRequest);
            }

            return $response;
        }
    }

    /**
     * @api {post} /v2/order/view View Order
     * @apiVersion 0.1.0
     * @apiName ViewOrder
     * @apiGroup Order
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}       gid            Order gid
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *     "gid": "04d3fe3fc74d0514ee93e208a52bcf90",
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *{
    "status": 200,
    "message": "OK",
    "order": {
        "or_id": 110,
        "or_gid": "a0758d1d8ded3efe62c465ad36987200",
        "or_uid": "or6047198783406",
        "or_name": "Order 1",
        "or_description": null,
        "or_status_id": 3,
        "or_pay_status_id": 1,
        "or_app_total": "229.00",
        "or_app_markup": null,
        "or_agent_markup": null,
        "or_client_total": "229.00",
        "or_client_currency": "USD",
        "or_client_currency_rate": "1.00000",
        "or_status_name": "Processing",
        "or_pay_status_name": "Not paid",
        "or_client_currency_symbol": "USD",
        "or_files": [],
        "or_request_uid": "OE96040",
        "billing_info": [
            {
                "bi_first_name": "Barbara Elmore",
                "bi_last_name": "T",
                "bi_middle_name": "",
                "bi_company_name": null,
                "bi_address_line1": "1013 Weda Cir",
                "bi_address_line2": null,
                "bi_city": "Mayfield",
                "bi_state": "KY",
                "bi_country": "US",
                "bi_zip": "99999",
                "bi_contact_phone": "+19074861000", -- deprecated, will be removed soon
                "bi_contact_email": "mike.kane@techork.com", -- deprecated, will be removed soon
                "bi_contact_name": null, -- deprecated, will be removed soon
                "bi_payment_method_id": 1,
                "bi_country_name": "United States of America",
                "bi_payment_method_name": "Credit / Debit Card"
            }
        ],
        "quotes": [
            {
                "pq_gid": "80e1ebef3057d60ff3870fe0a1eb83ee",
                "pq_name": "",
                "pq_order_id": 110,
                "pq_description": null,
                "pq_status_id": 3,
                "pq_price": 209,
                "pq_origin_price": 209,
                "pq_client_price": 209,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Applied",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 49,
                    "fq_source_id": null,
                    "fq_product_quote_id": 162,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDktMTcqTE9+I0xPNTE0I0xPMjgxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-03-11\",\"totalPrice\":209,\"totalTax\":123,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":43,\"pubBaseFare\":43,\"baseTax\":61.5,\"markup\":0,\"comm\":0,\"price\":104.5,\"tax\":61.5,\"oBaseFare\":{\"amount\":43,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.5,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-09-17 14:30\",\"arrivalTime\":\"2021-09-17 15:20\",\"stop\":0,\"stops\":[],\"flightNumber\":\"514\",\"bookingClass\":\"V\",\"duration\":110,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"WAW\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"LO\",\"airEquipType\":\"DH4\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"I\",\"mileage\":508,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-09-18 07:30\",\"arrivalTime\":\"2021-09-18 09:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"281\",\"bookingClass\":\"V\",\"duration\":175,\"departureAirportCode\":\"WAW\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"LO\",\"airEquipType\":\"738\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"O\",\"mileage\":893,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":1255}],\"maxSeats\":5,\"paxCnt\":2,\"validatingCarrier\":\"LO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"b3355dee-c859-4617-bca4-50046effc830\",\"availabilitySources\":\"S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":5}},\"ngsFeatures\":{\"stars\":1,\"name\":\"ECONOMY SAVER\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wOS0xNw==\",\"lang\":\"en\",\"rank\":6,\"cheapest\":true,\"fastest\":false,\"best\":false,\"bags\":0,\"country\":\"us\"},\"price\":104.5,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-09-17 14:30\",\"arrival\":\"2021-09-18 09:25\"}],\"bagFilter\":\"\",\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1255],\"totalDuration\":1255,\"topCriteria\":\"cheapest\",\"rank\":6}",
                    "fq_last_ticket_date": "2021-03-11",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 78,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "fqt_id": 103,
                            "fqt_uid": "fqt6047195e6a882",
                            "fqt_key": null,
                            "fqt_duration": 1255,
                            "segments": [
                                {
                                    "fqs_uid": "fqs6047195e6be4b",
                                    "fqs_departure_dt": "2021-09-17 14:30:00",
                                    "fqs_arrival_dt": "2021-09-17 15:20:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 514,
                                    "fqs_booking_class": "V",
                                    "fqs_duration": 110,
                                    "fqs_departure_airport_iata": "KIV",
                                    "fqs_departure_airport_terminal": "",
                                    "fqs_arrival_airport_iata": "WAW",
                                    "fqs_arrival_airport_terminal": "",
                                    "fqs_operating_airline": "LO",
                                    "fqs_marketing_airline": "LO",
                                    "fqs_air_equip_type": "DH4",
                                    "fqs_marriage_group": "I",
                                    "fqs_cabin_class": "Y",
                                    "fqs_meal": "",
                                    "fqs_fare_code": "V1SAV28",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "fqs_mileage": 508,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Warsaw",
                                    "operating_airline": "LOT Polish Airlines",
                                    "marketing_airline": "LOT Polish Airlines",
                                    "baggages": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 261,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "fqs_uid": "fqs6047195e6d5a0",
                                    "fqs_departure_dt": "2021-09-18 07:30:00",
                                    "fqs_arrival_dt": "2021-09-18 09:25:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 281,
                                    "fqs_booking_class": "V",
                                    "fqs_duration": 175,
                                    "fqs_departure_airport_iata": "WAW",
                                    "fqs_departure_airport_terminal": "",
                                    "fqs_arrival_airport_iata": "LHR",
                                    "fqs_arrival_airport_terminal": "2",
                                    "fqs_operating_airline": "LO",
                                    "fqs_marketing_airline": "LO",
                                    "fqs_air_equip_type": "738",
                                    "fqs_marriage_group": "O",
                                    "fqs_cabin_class": "Y",
                                    "fqs_meal": "",
                                    "fqs_fare_code": "V1SAV28",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "fqs_mileage": 893,
                                    "departureLocation": "Warsaw",
                                    "arrivalLocation": "London",
                                    "operating_airline": "LOT Polish Airlines",
                                    "marketing_airline": "LOT Polish Airlines",
                                    "baggages": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 262,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "43.00",
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6047195e6767d",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": "Alex",
                            "fp_last_name": "Grub",
                            "fp_middle_name": "",
                            "fp_dob": "1963-04-07"
                        },
                        {
                            "fp_uid": "fp6047195e67b7a",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": "Test name",
                            "fp_last_name": "Test last name",
                            "fp_middle_name": "Test middle name",
                            "fp_dob": "1963-04-07"
                        },
                        {
                            "fp_uid": "fp6047302b6966f",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047302b69a86",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp60473031c44c4",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp60473031c47b9",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                },
                "product": {
                    "pr_gid": null,
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513110,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null,
                    "holder": {
                        "ph_first_name": "test",
                        "ph_last_name": "test",
                        "ph_email": "test@test.test",
                        "ph_phone_number": "+19074861000"
                    }
                },
                "productQuoteOptions": [
                    {
                        "pqo_name": "Travel Guard",
                        "pqo_description": "",
                        "pqo_status_id": null,
                        "pqo_price": 20,
                        "pqo_client_price": 20,
                        "pqo_extra_markup": null,
                        "pqo_request_data": null,
                        "productOption": {
                            "po_key": "travelGuard",
                            "po_name": "Travel Guard",
                            "po_description": ""
                        }
                    }
                ]
            }
        ]
    },
    "technical": {
        "action": "v2/order/view",
        "response_id": 507,
        "request_dt": "2021-03-09 12:10:22",
        "response_dt": "2021-03-09 12:10:23",
        "execution_time": 0.122,
        "memory_usage": 1563368
    },
    "request": {
        "gid": "a0758d1d8ded3efe62c465ad36987200"
    }
}
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     * "status": 422,
     * "message": "Error",
     * "errors": [
     * "Order is not found"
     * ],
     * "code": 12100,
     * "technical": {
     * "action": "v2/order/view",
     * "response_id": 397,
     * "request_dt": "2021-03-01 17:40:41",
     * "response_dt": "2021-03-01 17:40:41",
     * "execution_time": 0.017,
     * "memory_usage": 212976
     * },
     * "request": {
     * "gid": "5287f7f7ff5a28789518db64e946ea67s"
     * }
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *     "status": 400,
     *     "message": "Load data error",
     *     "errors": [
     *         "Not found Order data on POST request"
     *     ],
     *     "code": "18300",
     *     "technical": {
     *         "action": "v2/order/view",
     *         "response_id": 11933856,
     *         "request_dt": "2020-02-03 12:49:20",
     *         "response_dt": "2020-02-03 12:49:20",
     *         "execution_time": 0.017,
     *         "memory_usage": 114232
     *     },
     *     "request": []
     * }
     */
    public function actionView()
    {
        $form = new OrderViewForm();

        if (!$form->load(\Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Order data on POST request'),
                new CodeMessage(OrderCodeException::API_ORDER_VIEW_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(OrderCodeException::API_ORDER_VIEW_VALIDATE)
            );
        }

        try {
            $order = $this->orderRepository->findByGid($form->gid);

            return new SuccessResponse(
                new Message('order', $order->serialize())
            );
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }
    }

    /**
     * @api {get} /v2/order/get-file Get File
     * @apiVersion 0.2.0
     * @apiName GetFile
     * @apiGroup Order
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}       uid            File UID
     *
     *
     * @apiErrorExample {json} Error-Response (404):
     *
     * HTTP/1.1 404 Not Found
     * {
     *   "name": "Not Found",
     *   "message": "File is not found.",
     *   "code": 0,
     *   "status": 404,
     *   "type": "yii\\web\\NotFoundHttpException"
     * }
     */
    public function actionGetFile()
    {
        $uid = (string)Yii::$app->request->get('uid');

        $file = FileStorage::find()->byUid($uid)->one();

        if (!$file) {
            throw new NotFoundHttpException('File is not found.');
        }

        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            \Yii::$app->response->headers->add('Content-Type', $file->fs_mime_type);
            \Yii::$app->response->stream = $this->fileSystem->readStream($file->fs_path);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'View File storage error.',
                'error' => $e->getMessage(),
                'fileId' => $file->fs_id,
                'userId' => Auth::id(),
            ], 'API:OrderController:GetFile');
            \Yii::$app->response->headers->add('Content-Type', 'text/html');
            return $this->asJson(['message' => 'Server error.']);
        }
    }

    /**
     * @return \webapi\src\response\Response
     * @api {post} /v2/order/create-c2b Create Order c2b flow
     * @apiVersion 1.0.0
     * @apiName CreateOrderClickToBook
     * @apiGroup Order
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     * {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiParam {string{max 10}}               sourceCid       Source cid
     * @apiParam {string{max 7}}               bookingId       Booking id
     * @apiParam {string{max 255}}              fareId          Unique value of order
     * @apiParam {string="success","failed"{max 10}}               status       Status
     * @apiParam {string{max 5}}               languageId          Language Id
     * @apiParam {string{max 2}}               marketCountry       Market Country
     *
     * @apiParam {Object[]}             quotes                  Product quotes
     * @apiParam {string}               quotes.productKey       Product key
     * @apiParam {string="booked","failed"}               quotes.status           Status
     * @apiParam {string}               quotes.originSearchData       Product quote origin search data
     * @apiParam {string}               quotes.quoteOtaId          Product quote custom id
     * @apiParam {Object}               quotes.holder                         Holder Info
     * @apiParam {string{max 50}}       quotes.holder.firstName               Holder first name
     * @apiParam {string{max 50}}       quotes.holder.lastName                Holder last name
     * @apiParam {string{max 50}}       [quotes.holder.middleName]              Holder middle name
     * @apiParam {string{max 100}}      quotes.holder.email                   Holder email
     * @apiParam {string{max 20}}       quotes.holder.phone                   Holder phone
     *
     * @apiParam {Object[]}             quotes.options                        Quote Options
     * @apiParam {string{max 30}}       quotes.options.productOptionKey       Product option key
     * @apiParam {string{max 50}}       [quotes.options.name]                   Name
     * @apiParam {string}               [quotes.options.description]            Description
     * @apiParam {Decimal}              quotes.options.price                  Price
     *
     * @apiParam {Object}                           [quotes.flightPaxData][]      Flight pax data
     * @apiParam {string="ADT","CHD","INF"}         quotes.flightPaxData.type                  Pax type
     * @apiParam {string{max 40}}                   [quotes.flightPaxData.first_name]            First Name
     * @apiParam {string{max 40}}                   [quotes.flightPaxData.last_name]             Last Name
     * @apiParam {string{max 40}}                   [quotes.flightPaxData.middle_name]           Middle Name
     * @apiParam {string{max 5}}                    [quotes.flightPaxData.nationality]           Nationality
     * @apiParam {string{max 1}}                    [quotes.flightPaxData.gender]                Gender
     * @apiParam {string{format yyyy-mm-dd}}        [quotes.flightPaxData.birth_date]            Birth Date
     * @apiParam {string{max 100}}                  [quotes.flightPaxData.email]                 Email
     * @apiParam {string{max 5}}                    [quotes.flightPaxData.language]              Language
     * @apiParam {string{max 5}}                    [quotes.flightPaxData.citizenship]           Citizenship
     *
     * @apiParam {Object}                           [quotes.hotelPaxData][]      Flight pax data
     * @apiParam {string="ADT","CHD"}               quotes.hotelPaxData.type                    Pax type
     * @apiParam {string{max 40}}                   [quotes.hotelPaxData.first_name]            First Name
     * @apiParam {string{max 40}}                   [quotes.hotelPaxData.last_name]             Last Name
     * @apiParam {string{format yyyy-mm-dd}}        [quotes.hotelPaxData.birth_date]            Birth Date
     * @apiParam {integer}                          [quotes.hotelPaxData.age]                   Age
     * @apiParam {string}                           quotes.hotelPaxData.hotelRoomKey            Hotel Room Key
     *
     * @apiParam {Object}       quotes.hotelRequest                     Hotel Request data <code>required for hotel quotes</code>
     * @apiParam {string}       quotes.hotelRequest.destinationName     Destination Name
     * @apiParam {string}       quotes.hotelRequest.destinationCode     Destination Code
     * @apiParam {string}       quotes.hotelRequest.checkIn             Check In Date <code>format: yyyy-mm-dd</code>
     * @apiParam {string}       quotes.hotelRequest.checkOut            Check Out Date <code>format: yyyy-mm-dd</code>
     *
     * @apiParam {Object}               [billingInfo]               BillingInfo
     * @apiParam {string{max 30}}       [billingInfo.first_name]      First Name
     * @apiParam {string{max 30}}       [billingInfo.last_name]       Last Name
     * @apiParam {string{max 30}}       [billingInfo.middle_name]     Middle Name
     * @apiParam {string{max 50}}       [billingInfo.address]         Address
     * @apiParam {string{max 2}}        [billingInfo.country_id]      Country Id
     * @apiParam {string{max 30}}       [billingInfo.city]            City
     * @apiParam {string{max 40}}       [billingInfo.state]           State
     * @apiParam {string{max 10}}       [billingInfo.zip]             Zip
     * @apiParam {string{max 20}}       [billingInfo.phone]           Phone <code>Deprecated</code>
     * @apiParam {string{max 160}}      [billingInfo.email]           Email <code>Deprecated</code>
     *
     * @apiParam {Object}               [creditCard]                    Credit Card
     * @apiParam {string{max 50}}       [creditCard.holder_name]        Holder Name
     * @apiParam {string{max 20}}       creditCard.number               Credit Card Number
     * @apiParam {string}               [creditCard.type]               Credit Card type
     * @apiParam {string{max 18}}       creditCard.expiration           Credit Card expiration
     * @apiParam {string{max 4}}        creditCard.cvv                  Credit Card cvv
     *
     * @apiParam {Object[]}             contactsInfo                 BillingInfo
     * @apiParam {string{max 50}}       contactsInfo.first_name      First Name
     * @apiParam {string{max 50}}       [contactsInfo.last_name]       Last Name
     * @apiParam {string{max 50}}       [contactsInfo.middle_name]     Middle Name
     * @apiParam {string{max 20}}       [contactsInfo.phone]           Phone number
     * @apiParam {string{max 100}}      contactsInfo.email           Email
     *
     * @apiParam {Object}               [payment]                    Payment info
     * @apiParam {string{max 3}}        [payment.clientCurrency]     Client currency
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
            "sourceCid": "ACHUY23AS",
            "bookingId": "WCJ12C",
            "fareId": "A0EA9F-5cc2ce331e8bb3.16383647",
            "status": "success",
            "languageId": "en-US",
            "marketCountry": "US",
            "quotes": [
                {
                    "status": "booked",
                    "productKey": "flight",
                    "originSearchData": "{\"key\":\"2_QldLMTAxKlkxMDAwL0pGS1BBUjIwMjEtMDgtMDcqREx+I0RMOTE4MH5sYzplbl91cw==\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-04-05\",\"totalPrice\":354.2,\"totalTax\":229.2,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"baseFare\":125,\"pubBaseFare\":125,\"baseTax\":229.2,\"markup\":0,\"comm\":0,\"price\":354.2,\"tax\":229.2,\"oBaseFare\":{\"amount\":125,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":229.2,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-08-07 16:30\",\"arrivalTime\":\"2021-08-08 05:55\",\"stop\":0,\"stops\":[],\"flightNumber\":\"9180\",\"bookingClass\":\"E\",\"duration\":445,\"departureAirportCode\":\"JFK\",\"departureAirportTerminal\":\"1\",\"arrivalAirportCode\":\"CDG\",\"arrivalAirportTerminal\":\"2E\",\"operatingAirline\":\"AF\",\"airEquipType\":\"77W\",\"marketingAirline\":\"DL\",\"marriageGroup\":\"O\",\"mileage\":3629,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"686562\",\"brandName\":\"BASIC ECONOMY\",\"meal\":\"\",\"fareCode\":\"VH7L09B1\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":445}],\"maxSeats\":9,\"paxCnt\":1,\"validatingCarrier\":\"DL\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"9cbb17ae-40dd-4d94-83be-2f0eed47e9ad\",\"availabilitySources\":\"S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"QldLMTAxWTEwMDB8SkZLUEFSMjAyMS0wOC0wNw==\",\"lang\":\"en\",\"rank\":10,\"cheapest\":true,\"fastest\":false,\"best\":true,\"bags\":0,\"country\":\"us\",\"prod_types\":[\"PUB\"]}}",
                    "options": [
                        {
                            "productOptionKey": "travelGuard",
                            "name": "Travel Guard",
                            "description": "",
                            "price": 20
                        }
                    ],
                    "flightPaxData": [
                        {
                            "first_name": "Test name",
                            "last_name": "Test last name",
                            "middle_name": "Test middle name",
                            "nationality": "US",
                            "gender": "M",
                            "birth_date": "1963-04-07",
                            "email": "mike.kane@techork.com",
                            "language": "en-US",
                            "citizenship": "US",
                            "type": "ADT"
                        }
                    ],
                    "quoteOtaId": "asdff43fsgfdsv343ddx",
                    "holder": {
                        "firstName": "Test",
                        "lastName": "Test",
                        "middleName": "Test",
                        "email": "test@test.test",
                        "phone": "+19074861000"
                    }
                },
                {
                    "status": "booked",
                    "productKey": "hotel",
                    "originSearchData": "{\"categoryName\":\"3 STARS\",\"destinationName\":\"Chisinau\",\"zoneName\":\"Chisinau\",\"minRate\":135.92,\"maxRate\":285.94,\"currency\":\"USD\",\"code\":148030,\"name\":\"Cosmos Hotel\",\"description\":\"The hotel is situated in the heart of Chisinau, the capital of Moldova. It is perfectly located for access to the business centre, cultural institutions and much more. Chisinau Airport is only 15 minutes away and the railway station is less than 5 minutes away from the hotel.\\n\\nThe city hotel offers a choice of 150 rooms, 24-hour reception and check-out services in the lobby, luggage storage, a hotel safe, currency exchange facility and a cloakroom. There is lift access to the upper floors as well as an on-site restaurant and conference facilities. Internet access, a laundry service (fees apply) and free parking in the car park are also on offer to guests during their stay.\\n\\nAll the rooms are furnished with double or king-size beds and provide an en suite bathroom with a shower. Air conditioning, central heating, satellite TV, a telephone, mini fridge, radio and free wireless Internet access are also on offer.\\n\\nThere is a golf course about 12 km from the hotel.\\n\\nThe hotel restaurant offers a wide selection of local and European cuisine. Breakfast is served as a buffet and lunch and dinner can be chosen la carte.\",\"countryCode\":\"MD\",\"stateCode\":\"MD\",\"destinationCode\":\"KIV\",\"zoneCode\":1,\"latitude\":47.014293,\"longitude\":28.853371,\"categoryCode\":\"3EST\",\"categoryGroupCode\":\"GRUPO3\",\"accomodationType\":{\"code\":\"HOTEL\"},\"boardCodes\":[\"BB\",\"AI\",\"HB\",\"FB\",\"RO\"],\"segmentCodes\":[],\"address\":\"NEGRUZZI, 2\",\"postalCode\":\"MD2001\",\"city\":\"CHISINAU\",\"email\":\"info@hotel-cosmos.com\",\"phones\":[{\"type\":\"PHONEBOOKING\",\"number\":\"+37322890054\"},{\"type\":\"PHONEHOTEL\",\"number\":\"+37322837505\"},{\"type\":\"FAXNUMBER\",\"number\":\"+37322542744\"}],\"images\":[{\"url\":\"14/148030/148030a_hb_a_001.jpg\",\"type\":\"GEN\"}],\"web\":\"http://hotel-cosmos.com/\",\"lastUpdate\":\"2020-11-23\",\"s2C\":\"1*\",\"ranking\":14,\"serviceType\":\"HOTELBEDS\",\"groupKey\":\"2118121725\",\"totalAmount\":341.32,\"totalMarkup\":26.69,\"totalPublicAmount\":347.99,\"totalSavings\":6.67,\"totalEarnings\":3.34,\"rates\":[{\"code\":\"ROO.ST\",\"name\":\"Room Standard\",\"key\":\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|BB|B2B|1~1~0||N@06~~24ebc~-829367492~N~~~NOR~C98A4E21F1184B3161702850635900AWUS0000029001400030824ebc\",\"class\":\"NOR\",\"allotment\":3,\"type\":\"RECHECK\",\"paymentType\":\"AT_WEB\",\"boardCode\":\"BB\",\"boardName\":\"BED AND BREAKFAST\",\"rooms\":1,\"adults\":1,\"markup\":16.62,\"amount\":205.4,\"publicAmmount\":209.55,\"savings\":4.15,\"earnings\":2.08},{\"code\":\"ROO.ST\",\"name\":\"Room Standard\",\"key\":\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d\",\"class\":\"NOR\",\"allotment\":3,\"type\":\"RECHECK\",\"paymentType\":\"AT_WEB\",\"boardCode\":\"RO\",\"boardName\":\"ROOM ONLY\",\"rooms\":1,\"adults\":2,\"markup\":10.07,\"amount\":135.92,\"publicAmmount\":138.44,\"savings\":2.52,\"earnings\":1.26}]}",

                    "quoteOtaId": "asdfw43wfdswef3x",
                    "holder": {
                        "firstName": "Test 2",
                        "lastName": "Test 2",
                        "email": "test+2@test.test",
                        "phone": "+19074861000"
                    },
                    "hotelPaxData": [
                        {
                            "hotelRoomKey": "20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d",
                            "first_name": "Test",
                            "last_name": "Test",
                            "birth_date": "1963-04-07",
                            "age": "45",
                            "type": "ADT"
                        },
                        {
                            "hotelRoomKey": "20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d",
                            "first_name": "Mary",
                            "last_name": "Smith",
                            "birth_date": "1963-04-07",
                            "age": "32",
                            "type": "ADT"
                        }
                    ],
                    "hotelRequest": {
                        "destinationCode": "BGO",
                        "destinationName": "Norway, Bergen",
                        "checkIn": "2021-09-10",
                        "checkOut": "2021-09-30"
                    }
                }
            ],
            "creditCard": {
                "holder_name": "Barbara Elmore",
                "number": "1111111111111111",
                "type": "Visas",
                "expiration": "07 / 23",
                "cvv": "324"
            },
            "billingInfo": {
                "first_name": "Barbara Elmore",
                "middle_name": "",
                "last_name": "T",
                "address": "1013 Weda Cir",
                "country_id": "US",
                "city": "Mayfield",
                "state": "KY",
                "zip": "99999",
                "phone": "+19074861000", -- deprecated, will be removed soon
                "email": "barabara@test.com" -- deprecated, will be removed soon
            },
            "contactsInfo": [
                {
                    "first_name": "Barbara",
                    "last_name": "Elmore",
                    "middle_name": "",
                    "phone": "+19074861000",
                    "email": "barabara@test.com"
                },
                {
                    "first_name": "John",
                    "last_name": "Doe",
                    "middle_name": "",
                    "phone": "+19074865678",
                    "email": "john@test.com"
                }
            ],
            "payment": {
                "clientCurrency": "USD"
            }
        }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
            "status": 200,
            "message": "OK",
            "data": {
                "order_gid": "1588da7b87cd3b91cc1df4aed0d7aeba"
            }
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
            "status": 422,
            "message": "Validation error",
            "errors": {
                "quotes.0.productKey": [
                    "Product type not found by key: flights"
                ]
            },
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
            "status": 422,
            "message": "test",
            "detailError": {
                "product": "Flight",
                "quoteOtaId": "asdff43fsgfdsv343ddx"
            },
            "code": 15901,
            "errors": []
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
            "status": 422,
            "message": "Validation error",
            "errors": {
                "fareId": [
                    "Fare Id \"A0EA9F-5cc2ce331e8bb3.16383647\" has already been taken."
                ]
            },
            "code": 0
        }
     *
     * @return ErrorResponse|SuccessResponse
     */
    public function actionCreateC2b()
    {
        $request = Yii::$app->request;
        $form = new OrderCreateC2BForm();

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
            $orderRequest = OrderRequest::create($request->post(), OrderSourceType::C2B);
            $this->orderRequestRepository->save($orderRequest);

            $order = $this->transactionManager->wrap(function () use ($form, $request, $orderRequest) {
                $dto = new CreateOrderDTO(
                    null,
                    $form->payment->clientCurrency,
                    $request->post(),
                    OrderSourceType::C2B,
                    $orderRequest->orr_id,
                    $form->projectId,
                    $form->getOrderStatus(),
                    $form->fareId,
                    $form->languageId,
                    $form->marketCountry
                );
                $order = $this->orderManageService->createByC2bFlow($dto);

                foreach ($form->quotes as $quoteForm) {
                    $quoteForm->orderId = $order->or_id;
                    $quoteForm->bookingId = $form->bookingId;

                    $productType = $this->productTypeRepository->findByKey($quoteForm->productKey);
                    $productCreateForm = new ProductCreateForm();
                    $productCreateForm->pr_type_id = $productType->pt_id;
                    $productCreateForm->pr_project_id = $form->projectId;
                    $product = $this->productCreateService->handle($productCreateForm);
                    $childProduct = $product->getChildProduct();
                    if ($childProduct) {
                        $childProduct->getService()->c2bHandle($childProduct, $quoteForm);
                    }
                }

                $order->calculateTotalPrice();
                $order->recalculateProfitAmount();
                $this->orderRepository->save($order);

                $this->orderDataService->create(
                    $order->or_id,
                    $form->bookingId,
                    $form->sourceId,
                    $dto->languageId,
                    $dto->marketCountry,
                    OrderDataActions::API_ORDER_CREATE_C2B,
                    null
                );

                if (isset($form->creditCard)) {
                    $creditCard = CreditCard::create(
                        $form->creditCard->number,
                        $form->creditCard->holder_name,
                        $form->creditCard->expiration_month,
                        $form->creditCard->expiration_year,
                        $form->creditCard->cvv,
                        $form->creditCard->type_id,
                    );
                    $creditCard->updateSecureCardNumber();
                    $creditCard->updateSecureCvv();
                    $this->creditCardRepository->save($creditCard);
                }

                if (isset($form->billingInfo)) {
                    $billingInfo = BillingInfo::create(
                        $form->billingInfo->first_name,
                        $form->billingInfo->last_name,
                        $form->billingInfo->middle_name,
                        $form->billingInfo->address,
                        $form->billingInfo->city,
                        $form->billingInfo->state,
                        $form->billingInfo->country_id,
                        $form->billingInfo->zip,
                        $form->billingInfo->phone,
                        $form->billingInfo->email,
                        null,
                        $creditCard->cc_id ?? null,
                        $order->or_id
                    );
                    $this->billingInfoRepository->save($billingInfo);
                }

                if (isset($form->contactsInfo)) {
                    foreach ($form->contactsInfo as $contactInfoForm) {
                        $this->orderContactManageService->create(
                            $order->or_id,
                            $contactInfoForm->first_name,
                            $contactInfoForm->last_name,
                            $contactInfoForm->middle_name,
                            $contactInfoForm->email,
                            $contactInfoForm->phone,
                            $order->or_project_id
                        );
                    }
                }

                return $order;
            });

            $response = new SuccessResponse(
                new DataMessage(
                    new Message('order_gid', $order->or_gid),
                )
            );

            $orderRequest->successResponse(ArrayHelper::toArray($response));
            $this->orderRequestRepository->save($orderRequest);

            return $response;
        } catch (OrderC2BException $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'API::OrderController::actionCreateC2b::OrderC2BException');

            $response = new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage($e->getMessage()),
                new DetailErrorMessage([
                    'product' => $e->dto->product->getProductName(),
                    'quoteOtaId' => $e->dto->quoteOtaId
                ]),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            $code = $e->getCode();
            $message = $code >= 500 ? 'Internal Server Error. Try again letter.' : $e->getMessage();
            Yii::error(AppHelper::throwableFormatter($e), 'API::OrderController::actionCreateC2b::Throwable');

            $response = new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($message),
                new ErrorsMessage($message),
                new CodeMessage($code)
            );
        }

        if (isset($orderRequest)) {
            $orderRequest->errorResponse(ArrayHelper::toArray($response));
            $this->orderRequestRepository->save($orderRequest);
        }

        return $response;
    }

    private function isClickToBook($data): bool
    {
        return isset($data['FlightRequest']);
    }

    /**
     * @api {post} /v2/order/cancel Cancel Order
     * @apiVersion 0.2.0
     * @apiName CancelOrder
     * @apiGroup Order
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}       gid            Order gid
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *     "gid": "04d3fe3fc74d0514ee93e208a52bcf90"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *    "status": 200,
     *    "message": "OK",
     *    "code": 0,
     *    "technical": {
     *        "action": "v2/order/cancel",
     *        "response_id": 15629,
     *        "request_dt": "2021-04-01 09:03:11",
     *        "response_dt": "2021-04-01 09:03:11",
     *        "execution_time": 0.019,
     *        "memory_usage": 186192
     *    },
     *    "request": {
     *       "gid": "04d3fe3fc74d0514ee93e208a52bcf90"
     *    }
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found data on POST request"
     *       ],
     *       "code": 10,
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
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *          "gid": [
     *            "Gid is invalid."
     *         ]
     *     },
     *     "code": 20,
     *     "technical": {
     *           ...
     *     },
     *     "request": {
     *           ...
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Error",
     *     "errors": {
     *         "The order is not available for processing."
     *     },
     *     "code": 30,
     *     "technical": {
     *           ...
     *     },
     *     "request": {
     *           ...
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Error",
     *     "errors": {
     *         "Unable to process flight cancellation."
     *     },
     *     "code": 40,
     *     "technical": {
     *           ...
     *     },
     *     "request": {
     *           ...
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Error",
     *     "errors": {
     *         "Unable to process hotel cancellation."
     *     },
     *     "code": 50,
     *     "technical": {
     *           ...
     *     },
     *     "request": {
     *           ...
     *     }
     * }
     *
     */
    public function actionCancel()
    {
        $form = new CancelForm();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
                new CodeMessage(10)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(20)
            );
        }

        try {
            $this->cancelOrder->cancel($form->gid);
        } catch (\DomainException $e) {
            return new ErrorResponse(
                new MessageMessage('Error'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Order cancel error.',
                'error' => $e->getMessage(),
                'orderGid' => $form->gid
            ], 'OrderCancelFlow:actionCancel');
            return new ErrorResponse(
                new MessageMessage('Error'),
                new ErrorsMessage('Server error. Please try again later.'),
                new StatusCodeMessage(500)
            );
        }
        return new SuccessResponse(
            new CodeMessage(0)
        );
    }
}
