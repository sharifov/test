<?php

namespace webapi\modules\v2\controllers;

use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\FileSystem;
use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\exceptions\OrderCodeException;
use modules\order\src\forms\api\create\OrderCreateForm;
use modules\order\src\forms\api\view\OrderViewForm;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderApiManageService;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
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
use webapi\src\response\messages\RequestMessage;
use webapi\src\response\messages\SourceMessage;
use webapi\src\response\messages\Sources;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\ProxyResponse;
use webapi\src\response\SimpleResponse;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;
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
    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;
    /**
     * @var FileSystem
     */
    private FileSystem $fileSystem;

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
        $config = []
    ) {
        parent::__construct($id, $module, $logger, $config);
        $this->requestBo = $request;
        $this->offerRepository = $offerRepository;
        $this->orderManageService = $orderManageService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderRepository = $orderRepository;
        $this->fileSystem = $fileSystem;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
//        $behaviors['logger'] = [
//            'class' => SimpleLoggerBehavior::class,
//            'filter' => CreditCardFilter::class,
//        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'filter' => CreditCardFilter::class,
        ];
        if (isset($behaviors['logger'])) {
            unset($behaviors['logger']);
        }
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
     * @api {post} /v2/order/create Create Order
     * @apiVersion 0.2.0
     * @apiName CreateOrder
     * @apiGroup Orders
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     * {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {String}       offerGid                                            Offer gid
     * @apiParam {Object[]}     productQuotes                                       Product Quotes
     * @apiParam {String}       productQuotes.gid                                   Product Quote Gid
     * @apiParam {Object[]}     productQuotes.quoteOptions                          Quote Options
     * @apiParam {String}       productQuotes.quoteOptions.productOptionKey         Product option key
     * @apiParam {String}       productQuotes.quoteOptions.name                     Name
     * @apiParam {String}       productQuotes.quoteOptions.description              Description
     * @apiParam {Decimal}      productQuotes.quoteOptions.price                    Price
     *
     * @apiParam {Object}       payment                 Payment
     * @apiParam {String}       payment.type            Type
     * @apiParam {Integer}      payment.transactionId   Transaction Id
     * @apiParam {String}       payment.date            Date
     * @apiParam {Decimal}      payment.amount          Amount
     * @apiParam {String}       payment.currency        Currency
     *
     * @apiParam {Object}       billingInfo                 BillingInfo
     * @apiParam {string}       billingInfo.first_name      First Name
     * @apiParam {string}       billingInfo.last_name       Last Name
     * @apiParam {string}       billingInfo.middle_name     Middle Name
     * @apiParam {string}       billingInfo.address         Address
     * @apiParam {string}       billingInfo.country_id      Country Id
     * @apiParam {string}       billingInfo.city            City
     * @apiParam {string}       billingInfo.state           State
     * @apiParam {string}       billingInfo.zip             Zip
     * @apiParam {string}       billingInfo.phone           Phone
     * @apiParam {string}       billingInfo.email           Email
     *
     * @apiParam {Object}       creditCard                  Credit Card
     * @apiParam {String}       creditCard.holder_name      Holder Name
     * @apiParam {String}       creditCard.number           Credit Card Number
     * @apiParam {String}       creditCard.type             Credit Card type
     * @apiParam {String}       creditCard.expiration       Credit Card expiration
     * @apiParam {String}       creditCard.cvv              Credit Card cvv
     *
     * @apiParam {Object}       Request                 Request Data for BO
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     * "offerGid": "73c8bf13111feff52794883446461740",
     * "productQuotes": [
     * {
     * "gid": "aebf921f5a64a7ac98d4942ace67e498",
     * "productOptions": [
     * {
     * "productOptionKey": "travelGuard",
     * "name": "Travel Guard",
     * "description": "",
     * "price": 20.00
     * }
     * ]
     * },
     * {
     * "gid": "6fcfc43e977dabffe6a979ebdaddfvr2"
     * }
     * ],
     * "payment": {
     * "type": "card",
     * "transactionId": 1234567890,
     * "date": "2021-03-20",
     * "amount": 821.49,
     * "currency": "USD"
     * },
     * "billingInfo": {
     * "first_name": "Barbara Elmore",
     * "middle_name": "",
     * "last_name": "T",
     * "address": "1013 Weda Cir",
     * "country_id": "US",
     * "city": "Mayfield",
     * "state": "KY",
     * "zip": "99999",
     * "phone": "+19074861000",
     * "email": "mike.kane@techork.com"
     * },
     * "creditCard": {
     * "holder_name": "Barbara Elmore",
     * "number": "1111111111111111",
     * "type": "Visa",
     * "expiration": "07 / 23",
     * "cvv": "324"
     * },
     * "Request": {
     * "offerGid": "85a06c376a083f47e56b286b1265c160",
     * "offerUid": "of60264c1484090",
     * "apiKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
     * "source": "I1B1L1",
     * "subSource": "-",
     * "totalOrderAmount": 821.49,
     * "FlightRequest": {
     * "productGid": "c6ae37ae73380c773cadf28fc0af9db2",
     * "uid": "OE96040",
     * "email": "mike.kane@techork.com",
     * "marker": null,
     * "client_ip_address": "92.115.180.30",
     * "trip_protection_amount": "0",
     * "insurance_code": "P7",
     * "is_facilitate": 0,
     * "delay_change": false,
     * "is_b2b": false,
     * "uplift": false,
     * "alipay": false,
     * "user_country": "us",
     * "user_language": "en-US",
     * "user_time_format": "h:mm a",
     * "user_month_date_format": {
     * "long": "EEE MMM d",
     * "short": "MMM d",
     * "fullDateLong": "EEE MMM d",
     * "fullDateShort": "MMM d, YYYY"
     * },
     * "currency_symbol": "$",
     * "pnr": null
     * },
     * "HotelRequest": {
     * "productGid": "cdd82f2616f600f71a68e9399c51276e"
     * },
     * "DriverRequest": {
     * "productGid": "cdd82f2616f600f71a68e9399c51276e"
     * },
     * "AttractionRequest": {
     * "productGid": "cdd82f2616f600f71a68e9399c51276e"
     * },
     * "CruiseRequest": {
     * "productGid": "cdd82f2616f600f71a68e9399c51276e"
     * },
     * "Card": {
     * "user_id": null,
     * "nickname": "B****** E***** T",
     * "number": "************6444",
     * "type": "Visa",
     * "expiration_date": "07 \/ 2023",
     * "first_name": "Barbara Elmore",
     * "middle_name": "",
     * "last_name": "T",
     * "address": "1013 Weda Cir",
     * "country_id": "US",
     * "city": "Mayfield",
     * "state": "KY",
     * "zip": "99999",
     * "phone": "+19074861000",
     * "deleted": null,
     * "cvv": "***",
     * "auth_attempts": null,
     * "country": "United States",
     * "calling": "",
     * "client_ip_address": "92.115.180.30",
     * "email": "mike.kane@techork.com",
     * "document": null
     * },
     * "AirRouting": {
     * "results": [
     * {
     * "gds": "S",
     * "key": "2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=",
     * "pcc": "8KI0",
     * "cons": "GTT",
     * "keys": {
     * "services": {
     * "support": {
     * "amount": 75
     * }
     * },
     * "seatHoldSeg": {
     * "trip": 0,
     * "seats": 9,
     * "segment": 0
     * },
     * "verification": {
     * "headers": {
     * "X-Client-Ip": "92.115.180.30",
     * "X-Kiv-Cust-Ip": "92.115.180.30",
     * "X-Kiv-Cust-ipv": "0",
     * "X-Kiv-Cust-ssid": "ovago-dev-0484692",
     * "X-Kiv-Cust-direct": "true",
     * "X-Kiv-Cust-browser": "desktop"
     * }
     * }
     * },
     * "meta": {
     * "eip": 0,
     * "bags": 2,
     * "best": false,
     * "lang": "en",
     * "rank": 6,
     * "group1": "LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75",
     * "country": "us",
     * "fastest": false,
     * "noavail": false,
     * "cheapest": true,
     * "searchId": "T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw"
     * },
     * "cabin": "Y",
     * "trips": [
     * {
     * "tripId": 1,
     * "duration": 1150,
     * "segments": [
     * {
     * "meal": "D",
     * "stop": 0,
     * "cabin": "Y",
     * "stops": [],
     * "baggage": {
     * "ADT": {
     * "carryOn": true,
     * "airlineCode": "PR",
     * "allowPieces": 2,
     * "allowMaxSize": "UP TO 62 LINEAR INCHES\/158 LINEAR CENTIMETERS",
     * "allowMaxWeight": "UP TO 50 POUNDS\/23 KILOGRAMS"
     * }
     * },
     * "mileage": 7305,
     * "duration": 870,
     * "fareCode": "U9XBUS",
     * "segmentId": 1,
     * "arrivalTime": "2021-05-15 04:00",
     * "airEquipType": "773",
     * "bookingClass": "U",
     * "flightNumber": "103",
     * "departureTime": "2021-05-13 22:30",
     * "marriageGroup": "O",
     * "recheckBaggage": false,
     * "marketingAirline": "PR",
     * "operatingAirline": "PR",
     * "arrivalAirportCode": "MNL",
     * "departureAirportCode": "LAX",
     * "arrivalAirportTerminal": "2",
     * "departureAirportTerminal": "B"
     * },
     * {
     * "meal": "B",
     * "stop": 0,
     * "cabin": "Y",
     * "stops": [],
     * "baggage": {
     * "ADT": {
     * "carryOn": true,
     * "airlineCode": "PR",
     * "allowPieces": 2,
     * "allowMaxSize": "UP TO 62 LINEAR INCHES\/158 LINEAR CENTIMETERS",
     * "allowMaxWeight": "UP TO 50 POUNDS\/23 KILOGRAMS"
     * }
     * },
     * "mileage": 728,
     * "duration": 130,
     * "fareCode": "U9XBUS",
     * "segmentId": 2,
     * "arrivalTime": "2021-05-15 08:40",
     * "airEquipType": "321",
     * "bookingClass": "U",
     * "flightNumber": "890",
     * "departureTime": "2021-05-15 06:30",
     * "marriageGroup": "I",
     * "recheckBaggage": false,
     * "marketingAirline": "PR",
     * "operatingAirline": "PR",
     * "arrivalAirportCode": "TPE",
     * "departureAirportCode": "MNL",
     * "arrivalAirportTerminal": "1",
     * "departureAirportTerminal": "1"
     * }
     * ]
     * },
     * {
     * "tripId": 2,
     * "duration": 1490,
     * "segments": [
     * {
     * "meal": "H",
     * "stop": 0,
     * "cabin": "Y",
     * "stops": [],
     * "baggage": {
     * "ADT": {
     * "carryOn": true,
     * "airlineCode": "PR",
     * "allowPieces": 2,
     * "allowMaxSize": "UP TO 62 LINEAR INCHES\/158 LINEAR CENTIMETERS",
     * "allowMaxWeight": "UP TO 50 POUNDS\/23 KILOGRAMS"
     * }
     * },
     * "mileage": 728,
     * "duration": 145,
     * "fareCode": "U9XBUS",
     * "segmentId": 1,
     * "arrivalTime": "2021-06-20 12:05",
     * "airEquipType": "321",
     * "bookingClass": "U",
     * "flightNumber": "891",
     * "departureTime": "2021-06-20 09:40",
     * "marriageGroup": "O",
     * "recheckBaggage": false,
     * "marketingAirline": "PR",
     * "operatingAirline": "PR",
     * "arrivalAirportCode": "MNL",
     * "departureAirportCode": "TPE",
     * "arrivalAirportTerminal": "2",
     * "departureAirportTerminal": "1"
     * },
     * {
     * "meal": "D",
     * "stop": 0,
     * "cabin": "Y",
     * "stops": [],
     * "baggage": {
     * "ADT": {
     * "carryOn": true,
     * "airlineCode": "PR",
     * "allowPieces": 2,
     * "allowMaxSize": "UP TO 62 LINEAR INCHES\/158 LINEAR CENTIMETERS",
     * "allowMaxWeight": "UP TO 50 POUNDS\/23 KILOGRAMS"
     * }
     * },
     * "mileage": 7305,
     * "duration": 805,
     * "fareCode": "U9XBUS",
     * "segmentId": 2,
     * "arrivalTime": "2021-06-20 19:30",
     * "airEquipType": "773",
     * "bookingClass": "U",
     * "flightNumber": "102",
     * "departureTime": "2021-06-20 21:05",
     * "marriageGroup": "I",
     * "recheckBaggage": false,
     * "marketingAirline": "PR",
     * "operatingAirline": "PR",
     * "arrivalAirportCode": "LAX",
     * "departureAirportCode": "MNL",
     * "arrivalAirportTerminal": "B",
     * "departureAirportTerminal": "1"
     * }
     * ]
     * }
     * ],
     * "paxCnt": 1,
     * "prices": {
     * "comm": 0,
     * "isCk": false,
     * "ccCap": 16.900002,
     * "markup": 50,
     * "oMarkup": {
     * "amount": 50,
     * "currency": "USD"
     * },
     * "markupId": 8833,
     * "totalTax": 321.75,
     * "markupUid": "1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e",
     * "totalPrice": 767.75,
     * "lastTicketDate": "2021-03-31"
     * },
     * "currency": "USD",
     * "fareType": "SR",
     * "maxSeats": 9,
     * "tripType": "RT",
     * "penalties": {
     * "list": [
     * {
     * "type": "re",
     * "permitted": false,
     * "applicability": "before"
     * },
     * {
     * "type": "re",
     * "permitted": false,
     * "applicability": "after"
     * },
     * {
     * "type": "ex",
     * "amount": 425,
     * "oAmount": {
     * "amount": 425,
     * "currency": "USD"
     * },
     * "permitted": true,
     * "applicability": "before"
     * },
     * {
     * "type": "ex",
     * "amount": 425,
     * "oAmount": {
     * "amount": 425,
     * "currency": "USD"
     * },
     * "permitted": true,
     * "applicability": "after"
     * }
     * ],
     * "refund": false,
     * "exchange": true
     * },
     * "routingId": 1,
     * "currencies": [
     * "USD"
     * ],
     * "founded_dt": "2021-02-25 13:44:54.570",
     * "passengers": {
     * "ADT": {
     * "cnt": 1,
     * "tax": 321.75,
     * "comm": 0,
     * "ccCap": 16.900002,
     * "price": 767.75,
     * "codeAs": "JCB",
     * "markup": 50,
     * "occCap": {
     * "amount": 16.900002,
     * "currency": "USD"
     * },
     * "baseTax": 271.75,
     * "oMarkup": {
     * "amount": 50,
     * "currency": "USD"
     * },
     * "baseFare": 446,
     * "oBaseTax": {
     * "amount": 271.75,
     * "currency": "USD"
     * },
     * "oBaseFare": {
     * "amount": 446,
     * "currency": "USD"
     * },
     * "pubBaseFare": 446
     * }
     * },
     * "ngsFeatures": {
     * "list": null,
     * "name": "",
     * "stars": 3
     * },
     * "currencyRates": {
     * "CADUSD": {
     * "to": "USD",
     * "from": "CAD",
     * "rate": 0.78417
     * },
     * "DKKUSD": {
     * "to": "USD",
     * "from": "DKK",
     * "rate": 0.16459
     * },
     * "EURUSD": {
     * "to": "USD",
     * "from": "EUR",
     * "rate": 1.23967
     * },
     * "GBPUSD": {
     * "to": "USD",
     * "from": "GBP",
     * "rate": 1.37643
     * },
     * "KRWUSD": {
     * "to": "USD",
     * "from": "KRW",
     * "rate": 0.00091
     * },
     * "MYRUSD": {
     * "to": "USD",
     * "from": "MYR",
     * "rate": 0.25006
     * },
     * "SEKUSD": {
     * "to": "USD",
     * "from": "SEK",
     * "rate": 0.12221
     * },
     * "TWDUSD": {
     * "to": "USD",
     * "from": "TWD",
     * "rate": 0.03592
     * },
     * "USDCAD": {
     * "to": "CAD",
     * "from": "USD",
     * "rate": 1.30086
     * },
     * "USDDKK": {
     * "to": "DKK",
     * "from": "USD",
     * "rate": 6.19797
     * },
     * "USDEUR": {
     * "to": "EUR",
     * "from": "USD",
     * "rate": 0.83926
     * },
     * "USDGBP": {
     * "to": "GBP",
     * "from": "USD",
     * "rate": 0.75587
     * },
     * "USDKRW": {
     * "to": "KRW",
     * "from": "USD",
     * "rate": 1117.1008
     * },
     * "USDMYR": {
     * "to": "MYR",
     * "from": "USD",
     * "rate": 4.07943
     * },
     * "USDSEK": {
     * "to": "SEK",
     * "from": "USD",
     * "rate": 8.34736
     * },
     * "USDTWD": {
     * "to": "TWD",
     * "from": "USD",
     * "rate": 28.96525
     * },
     * "USDUSD": {
     * "to": "USD",
     * "from": "USD",
     * "rate": 1
     * }
     * },
     * "validatingCarrier": "PR"
     * }
     * ],
     * "additionalInfo": {
     * "cabin": {
     * "C": "Business",
     * "F": "First",
     * "J": "Premium Business",
     * "P": "Premium First",
     * "S": "Premium Economy",
     * "Y": "Economy"
     * },
     * "airline": {
     * "PR": {
     * "name": "Philippine Airlines"
     * }
     * },
     * "airport": {
     * "LAX": {
     * "city": "Los Angeles",
     * "name": "Los Angeles International Airport",
     * "country": "United States"
     * },
     * "MNL": {
     * "city": "Manila",
     * "name": "Ninoy Aquino International Airport",
     * "country": "Philippines"
     * },
     * "TPE": {
     * "city": "Taipei",
     * "name": "Taiwan Taoyuan International Airport",
     * "country": "Taiwan"
     * }
     * },
     * "general": {
     * "tripType": "rt"
     * }
     * }
     * },
     * "Passengers": {
     * "Flight": [
     * {
     * "id": null,
     * "user_id": null,
     * "first_name": "Arthur",
     * "middle_name": "",
     * "last_name": "Davis",
     * "birth_date": "1963-04-07",
     * "gender": "M",
     * "seats": null,
     * "assistance": null,
     * "nationality": "US",
     * "passport_id": null,
     * "passport_valid_date": null,
     * "email": null,
     * "codeAs": null
     * }
     * ],
     * "Hotel": [
     * {
     * "first_name": "mike",
     * "last_name": "kane"
     * }
     * ],
     * "Driver": [
     * {
     * "first_name": "mike",
     * "last_name": "kane",
     * "age": "30-69",
     * "birth_date": "1973-04-07"
     * }
     * ],
     * "Attraction": [
     * {
     * "first_name": "mike",
     * "last_name": "kane",
     * "language_service": "US"
     * }
     * ],
     * "Cruise": [
     * {
     * "first_name": "Arthur",
     * "last_name": "Davis",
     * "citizenship": "US",
     * "birth_date": "1963-04-07",
     * "gender": "M"
     * }
     * ]
     * },
     * "Insurance": {
     * "total_amount": "20",
     * "record_id": "396393",
     * "passengers": [
     * {
     * "nameRef": "0",
     * "amount": 20
     * }
     * ]
     * },
     * "Tip": {
     * "total_amount": 20
     * },
     * "AuxiliarProducts": {
     * "Flight": {
     * "basket": {
     * "1c3df555-a2dc-4813-a055-2a8bf56fd8f1": {
     * "basket_item_id": "1c3df555-a2dc-4813-a055-2a8bf56fd8f1",
     * "benefits": [],
     * "display_name": "10kg Bag",
     * "price": {
     * "base": {
     * "amount": 2000,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 1820,
     * "currency": "USD",
     * "decimal_places": 2
     * }
     * },
     * "fees": [],
     * "markups": [
     * {
     * "amount": 600,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 546,
     * "currency": "USD",
     * "decimal_places": 2
     * },
     * "markup_type": "markup"
     * }
     * ],
     * "taxes": [
     * {
     * "amount": 200,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 182,
     * "currency": "USD",
     * "decimal_places": 2
     * },
     * "tax_type": "tax"
     * }
     * ],
     * "total": {
     * "amount": 2400,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 2184,
     * "currency": "USD",
     * "decimal_places": 2
     * }
     * }
     * },
     * "product_details": {
     * "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
     * "passenger_id": "p1",
     * "size": 150,
     * "size_unit": "cm",
     * "weight": 10,
     * "weight_unit": "kg"
     * },
     * "product_id": "741bcc97-c2fe-4820-b14d-f11f32e6fadb",
     * "product_type": "bag",
     * "quantity": 1,
     * "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
     * "validity": {
     * "state": "valid",
     * "valid_from": "2020-05-22T16:34:08Z",
     * "valid_to": "2020-05-22T16:49:08Z"
     * }
     * },
     * "2654f3f9-8990-4d2e-bdea-3b341ad5d1de": {
     * "basket_item_id": "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
     * "benefits": [],
     * "display_name": "Seat 15C",
     * "price": {
     * "base": {
     * "amount": 2000,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 1820,
     * "currency": "USD",
     * "decimal_places": 2
     * }
     * },
     * "fees": [],
     * "markups": [
     * {
     * "amount": 400,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 364,
     * "currency": "USD",
     * "decimal_places": 2
     * },
     * "markup_type": "markup"
     * }
     * ],
     * "taxes": [
     * {
     * "amount": 200,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": [],
     * "tax_type": "tax"
     * }
     * ],
     * "total": {
     * "amount": 2600,
     * "currency": "USD",
     * "decimal_places": 2,
     * "in_original_currency": {
     * "amount": 2366,
     * "currency": "USD",
     * "decimal_places": 2
     * }
     * }
     * },
     * "product_details": {
     * "column": "C",
     * "passenger_id": "p1",
     * "row": 15,
     * "segment_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
     * },
     * "product_id": "a17e10ca-0c9a-4691-9922-d664a3b52382",
     * "product_type": "seat",
     * "quantity": 1,
     * "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
     * "validity": {
     * "state": "valid",
     * "valid_from": "2020-05-22T16:34:08Z",
     * "valid_to": "2020-05-22T16:49:08Z"
     * }
     * },
     * "5d5e1bce-4577-4118-abcb-155823d8b4a3": [],
     * "6acd57ba-ccb7-4e86-85e7-b3e586caeae2": [],
     * "dffac4ba-73b9-4b1b-9334-001817fff0cf": [],
     * "e960eff9-7628-4645-99d8-20a6e22f6419": []
     * },
     * "country": "US",
     * "currency": "USD",
     * "journeys": [
     * {
     * "journey_id": "aab8980e-b263-4624-ad40-d6e5e364b4e9",
     * "segments": [
     * {
     * "arrival_airport": "LHR",
     * "arrival_time": "2020-07-07T22:30:00Z",
     * "departure_airport": "EDI",
     * "departure_time": "2020-07-07T21:10:00Z",
     * "fare_basis": "OTZ0RO\/Y",
     * "fare_class": "O",
     * "fare_family": "Basic Economy",
     * "marketing_airline": "BA",
     * "marketing_flight_number": "1465",
     * "number_of_stops": 0,
     * "operating_airline": "BA",
     * "operating_flight_number": "1465",
     * "segment_id": "938d8e82-dd7c-4d85-8ab4-38fea8753f6f"
     * }
     * ]
     * },
     * {
     * "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
     * "segments": [
     * {
     * "arrival_airport": "EDI",
     * "arrival_time": "2020-07-14T08:35:00Z",
     * "departure_airport": "LGW",
     * "departure_time": "2020-07-14T07:05:00Z",
     * "fare_basis": "NALZ0KO\/Y",
     * "fare_class": "N",
     * "fare_family": "Basic Economy",
     * "marketing_airline": "BA",
     * "marketing_flight_number": "2500",
     * "number_of_stops": 0,
     * "operating_airline": "BA",
     * "operating_flight_number": "2500",
     * "segment_id": "7d693cb0-d6d8-49f0-9489-866b3d789215"
     * }
     * ]
     * }
     * ],
     * "language": "en-US",
     * "orders": [],
     * "passengers": [
     * {
     * "first_names": "Vincent Willem",
     * "passenger_id": "ee850c82-e150-4f35-b0c7-228064c2964b",
     * "surname": "Van Gogh"
     * }
     * ],
     * "tickets": [
     * {
     * "basket_item_ids": [
     * "dffac4ba-73b9-4b1b-9334-001817fff0cf",
     * "e960eff9-7628-4645-99d8-20a6e22f6419",
     * "6acd57ba-ccb7-4e86-85e7-b3e586caeae2",
     * "5d5e1bce-4577-4118-abcb-155823d8b4a3"
     * ],
     * "journey_ids": [
     * "aab8980e-b263-4624-ad40-d6e5e364b4e9"
     * ],
     * "state": "in_basket",
     * "ticket_basket_item_id": "dffac4ba-73b9-4b1b-9334-001817fff0cf",
     * "ticket_id": "8c1c9fc8-d968-4733-93a8-6067bac2543f"
     * },
     * {
     * "basket_item_ids": [
     * "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
     * "1c3df555-a2dc-4813-a055-2a8bf56fd8f1"
     * ],
     * "journey_ids": [
     * "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
     * ],
     * "offered_price": {
     * "currency": "USD",
     * "decimal_places": 2,
     * "total": 20000
     * },
     * "state": "offered",
     * "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252"
     * }
     * ],
     * "trip_access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
     * "trip_id": "23259b86-3208-44c9-85cc-4b116a822bff",
     * "trip_state_hash": "69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee"
     * },
     * "Hotel": [],
     * "Driver": [],
     * "Attraction": [],
     * "Cruise": []
     * },
     * "Payment": {
     * "type": "CARD",
     * "transaction_id": "1234567890",
     * "card_id": 234567,
     * "auth_id": 123456
     * }
     * }
     * }
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
        $this->detachBehavior('request');
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

            $order = $this->orderManageService->createOrder((new CreateOrderDTO($offer->of_lead_id, $form->payment->currency, $request->post())), $form);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'API::OrderController::actionCreate::Throwable');
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

    /**
     * @api {post} /v2/order/view View Order
     * @apiVersion 0.1.0
     * @apiName ViewOrder
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
     *
     * {
    "status": 200,
    "message": "OK",
    "order": {
        "or_id": 96,
        "or_gid": "37442323c001ad23e92ec5afe2cbbcf3",
        "or_uid": "or6040db7958a98",
        "or_name": "Order 1",
        "or_description": null,
        "or_status_id": 3,
        "or_pay_status_id": 1,
        "or_app_total": "457.76",
        "or_app_markup": null,
        "or_agent_markup": null,
        "or_client_total": "457.76",
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
                "bi_contact_phone": "+19074861000",
                "bi_contact_email": "mike.kane@techork.com",
                "bi_contact_name": null,
                "bi_payment_method_id": 1,
                "bi_country_name": "United States of America",
                "bi_payment_method_name": "Credit / Debit Card"
            }
        ],
        "quotes": [
            {
                "pq_gid": "1a36f352a89dfb22b90b4d1d352033b2",
                "pq_name": "",
                "pq_order_id": 96,
                "pq_description": null,
                "pq_status_id": 3,
                "pq_price": 209.2,
                "pq_origin_price": 209.2,
                "pq_client_price": 209.2,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Applied",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 39,
                    "fq_source_id": null,
                    "fq_product_quote_id": 149,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDgtMTMqTE9+I0xPNTE0I0xPMjgxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-03-07\",\"totalPrice\":209.2,\"totalTax\":123.2,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":2,\"baseFare\":43,\"pubBaseFare\":43,\"baseTax\":61.6,\"markup\":0,\"comm\":0,\"price\":104.6,\"tax\":61.6,\"oBaseFare\":{\"amount\":43,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.6,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-08-13 14:30\",\"arrivalTime\":\"2021-08-13 15:20\",\"stop\":0,\"stops\":[],\"flightNumber\":\"514\",\"bookingClass\":\"V\",\"duration\":110,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"WAW\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"LO\",\"airEquipType\":\"DH4\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"I\",\"mileage\":508,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-08-14 07:30\",\"arrivalTime\":\"2021-08-14 09:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"281\",\"bookingClass\":\"V\",\"duration\":175,\"departureAirportCode\":\"WAW\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"LO\",\"airEquipType\":\"738\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"O\",\"mileage\":893,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":1255}],\"maxSeats\":9,\"paxCnt\":2,\"validatingCarrier\":\"LO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"950533dd-b6fd-4067-bad9-b3a6db0248b3\",\"availabilitySources\":\"S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"ECONOMY SAVER\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wOC0xMw==\",\"lang\":\"en\",\"rank\":7,\"cheapest\":true,\"fastest\":false,\"best\":false,\"bags\":0,\"country\":\"us\"},\"price\":104.6,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-08-13 14:30\",\"arrival\":\"2021-08-14 09:25\"}],\"bagFilter\":\"\",\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1255],\"totalDuration\":1255,\"topCriteria\":\"cheapest\",\"rank\":7}",
                    "fq_last_ticket_date": "2021-03-07",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 64,
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
                            "fqt_id": 94,
                            "fqt_key": null,
                            "fqt_duration": 1255,
                            "segments": [
                                {
                                    "fqs_departure_dt": "2021-08-13 14:30:00",
                                    "fqs_arrival_dt": "2021-08-13 15:20:00",
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
                                            "qsb_flight_quote_segment_id": 243,
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
                                    "fqs_departure_dt": "2021-08-14 07:30:00",
                                    "fqs_arrival_dt": "2021-08-14 09:25:00",
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
                                            "qsb_flight_quote_segment_id": 244,
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
                            "qpp_tax": "61.60",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.60",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.60",
                            "paxType": "ADT"
                        }
                    ]
                },
                "product": {
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513108,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": []
            },
            {
                "pq_gid": "b0ad06becae9f6ae663a3847f96bce72",
                "pq_name": "SGL.ST",
                "pq_order_id": 96,
                "pq_description": null,
                "pq_status_id": 3,
                "pq_price": 248.56,
                "pq_origin_price": 240.15,
                "pq_client_price": 248.56,
                "pq_service_fee_sum": 8.41,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Applied",
                "pq_files": [],
                "data": {
                    "hq_hash_key": "0ce58dc2980cc4be02daf0d2ebd60470",
                    "hq_destination_name": "Rome",
                    "hq_hotel_name": "Patria",
                    "hq_request_hash": "4fa6b9917ce93cc7caa3de2e250fb775",
                    "hq_booking_id": null,
                    "hq_json_booking": null,
                    "hq_check_in_date": "2021-06-17",
                    "hq_check_out_date": "2021-06-24",
                    "hotel_request": {
                        "ph_check_in_date": "2021-07-15",
                        "ph_check_out_date": "2021-07-22",
                        "ph_destination_code": "ROE",
                        "ph_destination_label": "Italy, Rome",
                        "destination_city": null
                    },
                    "hotel": {
                        "hl_name": "Patria",
                        "hl_star": "",
                        "hl_category_name": "3 STARS",
                        "hl_destination_name": "Rome",
                        "hl_zone_name": "Via Nazionale",
                        "hl_country_code": "IT",
                        "hl_state_code": "07",
                        "hl_description": "This hotel, of unparalleled beauty, offers comfortable and elegant rooms, fully equipped for a pleasant stay. Travelers looking to have a cultural adventure, learn about art, architecture, ruins and Roman history, are the favorites. With a privileged location just 14 kilometers from the Rome - Ciampino Airport, a few minutes from the Repubblica Metro Station and the Teatro dell'Opera, this hotel is surrounded by restaurants, markets, squares, museums and popular theaters, as well as It provides easy access to other important sites in the city, so it invites visitors to take out their spirit of adventure and curiosity in the Roman streets. The service staff is exceptional, distinguished by its excellent service and warm friendliness. Nobody misses the gastronomic delights of Rome in this hotel, from the cafeteria and the hotel menus, to the variety of restaurants that are just around the corner. The ideal place to vacation in the heart of Rome.",
                        "hl_address": "Via Torino, 36-37",
                        "hl_postal_code": "00184",
                        "hl_city": "ROME",
                        "hl_email": "INFO@HOTELPATRIA.IT",
                        "hl_web": null,
                        "hl_phone_list": [
                            {
                                "type": "PHONEBOOKING",
                                "number": "0039064880756"
                            },
                            {
                                "type": "PHONEHOTEL",
                                "number": "0039064880756"
                            },
                            {
                                "type": "PHONEMANAGEMENT",
                                "number": "0039064818254"
                            },
                            {
                                "type": "FAXNUMBER",
                                "number": "0039 064814872"
                            }
                        ],
                        "hl_image_list": [
                            {
                                "url": "01/017317/017317a_hb_a_007.jpg",
                                "type": "GEN"
                            }
                        ],
                        "hl_image_base_url": null,
                        "json_booking": null
                    },
                    "rooms": [
                        {
                            "hqr_room_name": "Single Standard",
                            "hqr_class": "NRF",
                            "hqr_amount": 222.36,
                            "hqr_currency": "USD",
                            "hqr_cancel_amount": "240.15",
                            "hqr_cancel_from_dt": "2021-03-03 22:59:00",
                            "hqr_board_name": "ROOM ONLY",
                            "hqr_rooms": 1,
                            "hqr_adults": 1,
                            "hqr_children": null
                        }
                    ]
                },
                "product": {
                    "pr_type_id": 2,
                    "pr_name": "",
                    "pr_lead_id": 513108,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": []
            }
        ]
    },
    "technical": {
        "action": "v2/order/view",
        "response_id": 478,
        "request_dt": "2021-03-04 13:07:42",
        "response_dt": "2021-03-04 13:07:42",
        "execution_time": 0.139,
        "memory_usage": 1844856
    },
    "request": {
        "gid": "37442323c001ad23e92ec5afe2cbbcf3"
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

    private function isClickToBook($data): bool
    {
        return isset($data['FlightRequest']);
    }
}
