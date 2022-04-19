<?php

namespace console\controllers;

use common\models\Department;
use common\models\Project;
use common\models\query\LeadFlowQuery;
use common\models\search\ContactsSearch;
use common\models\UserGroup;
use frontend\helpers\JsonHelper;
use Gnello\Mattermost\Driver;
use modules\cases\src\abac\CasesAbacObject;
use modules\lead\src\abac\LeadAbacObject;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeCreatedEvent;
use modules\user\src\update\UpdateForm;
use src\auth\Auth;
use src\helpers\setting\SettingHelper;
use src\model\call\useCase\createCall\CreateCallForm;
use src\model\client\notifications\client\entity\NotificationType;
use common\components\purifier\Purifier;
use common\models\CallUserAccess;
use common\models\Employee;
use common\models\Lead;
use common\models\Call;
use common\models\Notifications;
use common\models\Sms;
use common\models\UserOnline;
use Faker\Factory;
use frontend\widgets\notification\NotificationMessage;
use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\src\useCases\reprotectionDecision\confirm\BoRequest;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookService;
use modules\lead\src\services\LeadFailBooking;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\jobs\OrderCanceledConfirmationJob;
use modules\order\src\payment\services\PaymentService;
use modules\order\src\processManager\phoneToBook\events\FlightQuoteBookedEvent;
use modules\order\src\processManager\phoneToBook\events\QuoteBookedEvent;
use modules\order\src\processManager\jobs\BookingFlightJob;
use modules\order\src\processManager\jobs\BookingHotelJob;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\services\confirmation\EmailConfirmationSender;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use src\dispatchers\EventDispatcher;
use src\helpers\LogExecutionTime;
use src\model\cases\useCases\cases\api\create\Command;
use src\model\cases\useCases\cases\api\create\Handler;
use src\model\client\useCase\excludeInfo\ClientExcludeIpChecker;
use src\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use src\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use src\model\clientChat\ClientChatPlatform;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use src\model\clientChat\entity\projectConfig\ProjectConfigApiResponseDto;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatMessage\ClientChatMessageRepository;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use src\model\clientChatUnread\entity\ClientChatUnread;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use src\model\conference\entity\aggregate\ConferenceLogAggregate;
use src\model\conference\entity\aggregate\Duration;
use src\model\conference\entity\aggregate\log\HtmlFormatter;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLog;
use src\model\conference\entity\conferenceEventLog\EventFactory;
use src\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use src\model\conference\useCase\PrepareCurrentCallsForNewCall;
use src\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use src\model\department\department\DefaultPhoneType;
use src\model\leadRedial\assign\LeadRedialAccessChecker;
use src\model\leadRedial\assign\Users;
use src\model\leadRedial\entity\CallRedialUserAccess;
use src\model\leadRedial\entity\CallRedialUserAccessRepository;
use src\model\leadRedial\priorityLevel\ConversionFetcher;
use src\model\leadRedial\queue\CallNextLeads;
use src\model\leadRedial\queue\RedialCall;
use src\model\phoneNumberRedial\entity\Scopes\PhoneNumberRedialQuery;
use src\model\project\entity\params\Params;
use src\model\user\reports\stats\UserStatsReport;
use src\model\voip\phoneDevice\device\ReadyVoipDevice;
use src\model\voip\phoneDevice\PhoneDeviceLogForm;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;
use src\services\sms\incoming\SmsIncomingForm;
use src\services\sms\incoming\SmsIncomingService;
use src\websocketServer\healthCheck\WebsocketHealthChecker;
use Swoole\Http\Client;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\ExitCode;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\rbac\Role;

class TestController extends Controller
{
    public function actionRr()
    {
        $user = Employee::findOne(294);
        $updatedUser = Employee::findOne(295);
        $form = new UpdateForm($user, $updatedUser);
//        $form->form_roles = ['admin', 'agent_qw'];
//        $form->validate();
        VarDumper::dump($form->getAttributes(null, ['targetUser', 'updaterUser']));
//        VarDumper::dump($form->getErrors());
    }

    public function actionTestWs()
    {
        Notifications::publish(
            'testCommand',
            ['user_id' => 295],
            [
                'data' => 'testData',
            ]
        );
    }

    public function actionT()
    {
        VarDumper::dump(CasesAbacObject::getObjectAttributeList());
    }
    public function actionWsHealthCheck()
    {
        $checker = new WebsocketHealthChecker();
        $result = $checker->check(
            'ws://' . env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_HOST') . ':' . env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_PORT'),
            5,
            'qwerty'
        );
        VarDumper::dump($result);
    }

    public function actionPending()
    {
        $access = CallUserAccess::find()->andWhere(['cua_call_id' => 3386771, 'cua_user_id' => 295])->one();
        $access->acceptPending();
        $access->save();
    }

    public function actionAccept()
    {
        $access = CallUserAccess::find()->andWhere(['cua_call_id' => 3386771, 'cua_user_id' => 295])->one();
        $access->acceptCall();
        $access->save();
    }

    public function actionVoip()
    {
        $ids = (new ReadyVoipDevice())->findBrowserGroupIds(295);
        VarDumper::dump($ids);

        die;
//        $f = new PhoneDeviceLogForm();
//        $f->load([
//            'level' => 4,
//            'message' => 'Received transportClose from pstream',
//            'timestamp' => 1638639604649,
//            'stacktrace' => '    at Log.error (https://sales.local:444/twilio/twilio-2.0.1.js:4231:32)n    at PStream.Call._this._onTransportClose (https://sales.local:444/twilio/twilio-2.0.1.js:1127:24)n    at emitNone (https://sales.local:444/twilio/twilio-2.0.1.js:10547:13)n    at PStream.emit (https://sales.local:444/twilio/twilio-2.0.1.js:10632:7)n    at PStream._handleTransportClose (https://sales.local:444/twilio/twilio-2.0.1.js:5100:8)n    at emitNone (https://sales.local:444/twilio/twilio-2.0.1.js:10547:13)n    at WSTransport.emit (https://sales.local:444/twilio/twilio-2.0.1.js:10632:7)n    at WSTransport._closeSocket (https://sales.local:444/twilio/twilio-2.0.1.js:9342:14)',
//        ]);
//        $f->validate();
//        VarDumper::dump($f->getErrors());
//        die;

        $m = <<<JSON
{\"message\":\"ConnectionError (31005): Error sent from gateway in HANGUP\",\"causes\":[],\"code\":31005,\"description\":\"Connection error\",\"explanation\":\"A connection error occurred during the call\",\"name\":\"ConnectionError\",\"solutions\":[]}
JSON;
//        $m = preg_replace("/\n/", "", $m);
        $m = preg_replace('/\\\"/', "\"", $m);
//        VarDumper::dump($m);die;


//        $m = str_replace('\"', '"', $m);
//        VarDumper::dump($m);die;
        $a = json_decode((($m)), true, 512, JSON_THROW_ON_ERROR);

        VarDumper::dump($a);
        die;

//        die;
        $contacts = (new ContactsSearch(295))->getClientsContactByPhone('+37368852225');
        VarDumper::dump($contacts);


        die;
        $form = new CreateCallForm(295);
        $form->load([
            //'toUserId' => '1',
            'to' => '+14155769359',
            'fromLead' => '1'
        ]);
        $form->validate();
        VarDumper::dump($form->getErrors());
        VarDumper::dump($form->getAttributes());
    }

    public function actionMmm()
    {
        $user = Employee::findOne(295);
        $report = new UserStatsReport(
            $user->timezone,
            date('Y-m') . '-01 00:00 - ' . date('Y-m-d') . ' 23:59',
            Department::getList(),
            array_map(fn (Role $item) => $item->description, \Yii::$app->authManager->getRoles()),
            UserGroup::getList(),
            Employee::getActiveUsersList()
        );
        $params['UserStatsReport']['departments'] = [
            1, 2, 3
        ];
        $params['UserStatsReport']['groups'] = [
            1
        ];
        $params['UserStatsReport']['users'] = [
            295, 294
        ];

        $report->search($params);
        VarDumper::dump($report->getErrors());
        die;
        $users = \Yii::createObject(Users::class);
        $lead = Lead::findOne(513195);
        $r = $users->getUsers($lead, 4, false);
        VarDumper::dump($r);
    }

    public function actionNnn()
    {
        $call = Call::findOne(3385614);
        $r = Employee::getUsersForCallQueue($call, 10);
        VarDumper::dump($r);
    }

    public function actionR()
    {
        $access = CallRedialUserAccess::find()->andWhere(['crua_lead_id' => 513195, 'crua_user_id' => 295])->one();
        $repo = \Yii::createObject(CallRedialUserAccessRepository::class);
        $repo->remove($access);
        die;
    }

    public function actionA()
    {
        $repo = \Yii::createObject(CallRedialUserAccessRepository::class);
        $access = CallRedialUserAccess::create(513195, 295, new \DateTimeImmutable());
        $repo->save($access);
        die;


//        \Yii::createObject(\src\model\client\notifications\listeners\productQuoteChangeDecided\ClientNotificationCancelerListener::class)->handle(new ProductQuoteChangeDecisionModifyEvent(113, 192));
//        die;

//        \Yii::createObject(ClientNotificationExecutor::class)->execute(2);
//        die;
        $repo = \Yii::createObject(ProductQuoteChangeRepository::class);
        $change = ProductQuoteChange::createNew(
            193,
            135988
        );
        $repo->save($change);


//        $repo = \Yii::createObject(ProductQuoteChangeRepository::class);
//        $change = ProductQuoteChange::createNew(
//            192,
//            135987
//        );
//        $repo->save($change);
    }

    public function actionQ()
    {
        echo \Yii::$app->communication->makeCallClientNotification(
            '+14157693509',
            '+37369305726',
            'Hello world',
            'woman',
            null,
            null,
            [
                'project_id' => 2,
                'client_id' => 472969,
                'case_id' => 135981,
                'phone_list_id' => 1468,
            ]
        );
    }

    public function actionX()
    {

        $productQuote = ProductQuote::find()->andWhere(['pq_gid' => '1865ef55f3c6c01dca1f4f3128e82733'])->one();
        $r = ArrayHelper::toArray($productQuote);
        VarDumper::dump($r);
            die;
    }
    /*public function actionSmsNotify()
    {
        $smsIncomingForm  = new SmsIncomingForm();
        $smsIncomingForm->si_phone_from = '+19173649747';
        $smsIncomingForm->si_phone_to = '+16693337271';
        $smsIncomingForm->si_sms_text = 'Test Sms message to lead vincent dev';

        $response = (\Yii::createObject(SmsIncomingService::class))->create($smsIncomingForm)->attributes;

        print_r($response);

        echo 'Local sms notification simulation Done';
    }*/

    /*public function actionCallNotify()
    {
        $call =  Call::find()->where(['c_id' => 3368237])->one();

        if ($sendWarmTransferMissedNotification = true) {
            $message = 'Missed Call (Id: ' . Purifier::createCallShortLink($call) . ')  from ';
            if ($call->c_lead_id && $call->cLead) {
                $message .= $call->cLead->client ? $call->cLead->client->getFullName() : '';
                $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($call->cLead) . ')';
                $message .= $call->cLead->project ? '<br> ' . $call->cLead->project->name : '';
            }
            if ($call->c_case_id && $call->cCase) {
                $message .= $call->cCase->client ? $call->cLead->client->getFullName() : '';
                $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($call->cCase) . ')';
                $message .= $call->cCase->project ? '<br> ' . $call->cCase->project->name : '';
            }

            if (
                $ntf = Notifications::create(
                    658,
                    'Missed Call (' . Call::SOURCE_LIST[Call::SOURCE_TRANSFER_CALL] . ')',
                    $message,
                    Notifications::TYPE_WARNING,
                    true
                )
            ) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => 658], $dataNotification);
            }
        }

        print_r($call);
    }*/

    public function actionDisconnectCalls()
    {
        $userId = 295;
        $prepare = new PrepareCurrentCallsForNewCall($userId);
        $prepare->prepare();
    }

    public function actionTest()
    {
        $s = \Yii::createObject(LeadFailBooking::class);
        $s->create(759, null);

//        $t = (json_decode('{"Request": {"Tip": {"total_amount": 20}, "Card": {"cvv": "***", "zip": "99999", "city": "Mayfield", "type": "Visa", "email": "mike.kane@techork.com", "phone": "+19074861000", "state": "KY", "number": "************6444", "address": "1013 Weda Cir", "calling": "", "country": "United States", "deleted": null, "user_id": null, "document": null, "nickname": "B****** E***** T", "last_name": "T", "country_id": "US", "first_name": "Barbara Elmore", "middle_name": "", "auth_attempts": null, "expiration_date": "07 / 2023", "client_ip_address": "92.115.180.30"}, "apiKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826", "source": "I1B1L1", "Payment": {"type": "CARD", "card_id": 234567, "transaction_id": "1234567890"}, "offerGid": "85a06c376a083f47e56b286b1265c160", "offerUid": "of60264c1484090", "Insurance": {"record_id": "396393", "passengers": [{"amount": 20, "nameRef": "0"}], "total_amount": "20"}, "subSource": "-", "AirRouting": {"results": [{"gds": "S", "key": "2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=", "pcc": "8KI0", "cons": "GTT", "keys": {"services": {"support": {"amount": 75}}, "seatHoldSeg": {"trip": 0, "seats": 9, "segment": 0}, "verification": {"headers": {"X-Client-Ip": "92.115.180.30", "X-Kiv-Cust-Ip": "92.115.180.30", "X-Kiv-Cust-ipv": "0", "X-Kiv-Cust-ssid": "ovago-dev-0484692", "X-Kiv-Cust-direct": "true", "X-Kiv-Cust-browser": "desktop"}}}, "meta": {"eip": 0, "bags": 2, "best": false, "lang": "en", "rank": 6, "group1": "LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75", "country": "us", "fastest": false, "noavail": false, "cheapest": true, "searchId": "T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw"}, "cabin": "Y", "trips": [{"tripId": 1, "duration": 1150, "segments": [{"meal": "D", "stop": 0, "cabin": "Y", "stops": [], "baggage": {"ADT": {"carryOn": true, "airlineCode": "PR", "allowPieces": 2, "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS", "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"}}, "mileage": 7305, "duration": 870, "fareCode": "U9XBUS", "segmentId": 1, "arrivalTime": "2021-05-15 04:00", "airEquipType": "773", "bookingClass": "U", "flightNumber": "103", "departureTime": "2021-05-13 22:30", "marriageGroup": "O", "recheckBaggage": false, "marketingAirline": "PR", "operatingAirline": "PR", "arrivalAirportCode": "MNL", "departureAirportCode": "LAX", "arrivalAirportTerminal": "2", "departureAirportTerminal": "B"}, {"meal": "B", "stop": 0, "cabin": "Y", "stops": [], "baggage": {"ADT": {"carryOn": true, "airlineCode": "PR", "allowPieces": 2, "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS", "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"}}, "mileage": 728, "duration": 130, "fareCode": "U9XBUS", "segmentId": 2, "arrivalTime": "2021-05-15 08:40", "airEquipType": "321", "bookingClass": "U", "flightNumber": "890", "departureTime": "2021-05-15 06:30", "marriageGroup": "I", "recheckBaggage": false, "marketingAirline": "PR", "operatingAirline": "PR", "arrivalAirportCode": "TPE", "departureAirportCode": "MNL", "arrivalAirportTerminal": "1", "departureAirportTerminal": "1"}]}, {"tripId": 2, "duration": 1490, "segments": [{"meal": "H", "stop": 0, "cabin": "Y", "stops": [], "baggage": {"ADT": {"carryOn": true, "airlineCode": "PR", "allowPieces": 2, "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS", "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"}}, "mileage": 728, "duration": 145, "fareCode": "U9XBUS", "segmentId": 1, "arrivalTime": "2021-06-20 12:05", "airEquipType": "321", "bookingClass": "U", "flightNumber": "891", "departureTime": "2021-06-20 09:40", "marriageGroup": "O", "recheckBaggage": false, "marketingAirline": "PR", "operatingAirline": "PR", "arrivalAirportCode": "MNL", "departureAirportCode": "TPE", "arrivalAirportTerminal": "2", "departureAirportTerminal": "1"}, {"meal": "D", "stop": 0, "cabin": "Y", "stops": [], "baggage": {"ADT": {"carryOn": true, "airlineCode": "PR", "allowPieces": 2, "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS", "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"}}, "mileage": 7305, "duration": 805, "fareCode": "U9XBUS", "segmentId": 2, "arrivalTime": "2021-06-20 19:30", "airEquipType": "773", "bookingClass": "U", "flightNumber": "102", "departureTime": "2021-06-20 21:05", "marriageGroup": "I", "recheckBaggage": false, "marketingAirline": "PR", "operatingAirline": "PR", "arrivalAirportCode": "LAX", "departureAirportCode": "MNL", "arrivalAirportTerminal": "B", "departureAirportTerminal": "1"}]}], "paxCnt": 1, "prices": {"comm": 0, "isCk": false, "ccCap": 16.900002, "markup": 50, "oMarkup": {"amount": 50, "currency": "USD"}, "markupId": 8833, "totalTax": 321.75, "markupUid": "1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e", "totalPrice": 767.75, "lastTicketDate": "2021-03-31"}, "currency": "USD", "fareType": "SR", "maxSeats": 9, "tripType": "RT", "penalties": {"list": [{"type": "re", "permitted": false, "applicability": "before"}, {"type": "re", "permitted": false, "applicability": "after"}, {"type": "ex", "amount": 425, "oAmount": {"amount": 425, "currency": "USD"}, "permitted": true, "applicability": "before"}, {"type": "ex", "amount": 425, "oAmount": {"amount": 425, "currency": "USD"}, "permitted": true, "applicability": "after"}], "refund": false, "exchange": true}, "routingId": 1, "currencies": ["USD"], "founded_dt": "2021-02-25 13:44:54.570", "passengers": {"ADT": {"cnt": 1, "tax": 321.75, "comm": 0, "ccCap": 16.900002, "price": 767.75, "codeAs": "JCB", "markup": 50, "occCap": {"amount": 16.900002, "currency": "USD"}, "baseTax": 271.75, "oMarkup": {"amount": 50, "currency": "USD"}, "baseFare": 446, "oBaseTax": {"amount": 271.75, "currency": "USD"}, "oBaseFare": {"amount": 446, "currency": "USD"}, "pubBaseFare": 446}}, "ngsFeatures": {"list": null, "name": "", "stars": 3}, "currencyRates": {"CADUSD": {"to": "USD", "from": "CAD", "rate": 0.78417}, "DKKUSD": {"to": "USD", "from": "DKK", "rate": 0.16459}, "EURUSD": {"to": "USD", "from": "EUR", "rate": 1.23967}, "GBPUSD": {"to": "USD", "from": "GBP", "rate": 1.37643}, "KRWUSD": {"to": "USD", "from": "KRW", "rate": 0.00091}, "MYRUSD": {"to": "USD", "from": "MYR", "rate": 0.25006}, "SEKUSD": {"to": "USD", "from": "SEK", "rate": 0.12221}, "TWDUSD": {"to": "USD", "from": "TWD", "rate": 0.03592}, "USDCAD": {"to": "CAD", "from": "USD", "rate": 1.30086}, "USDDKK": {"to": "DKK", "from": "USD", "rate": 6.19797}, "USDEUR": {"to": "EUR", "from": "USD", "rate": 0.83926}, "USDGBP": {"to": "GBP", "from": "USD", "rate": 0.75587}, "USDKRW": {"to": "KRW", "from": "USD", "rate": 1117.1008}, "USDMYR": {"to": "MYR", "from": "USD", "rate": 4.07943}, "USDSEK": {"to": "SEK", "from": "USD", "rate": 8.34736}, "USDTWD": {"to": "TWD", "from": "USD", "rate": 28.96525}, "USDUSD": {"to": "USD", "from": "USD", "rate": 1}}, "validatingCarrier": "PR"}], "additionalInfo": {"cabin": {"C": "Business", "F": "First", "J": "Premium Business", "P": "Premium First", "S": "Premium Economy", "Y": "Economy"}, "airline": {"PR": {"name": "Philippine Airlines"}}, "airport": {"LAX": {"city": "Los Angeles", "name": "Los Angeles International Airport", "country": "United States"}, "MNL": {"city": "Manila", "name": "Ninoy Aquino International Airport", "country": "Philippines"}, "TPE": {"city": "Taipei", "name": "Taiwan Taoyuan International Airport", "country": "Taiwan"}}, "general": {"tripType": "rt"}}}, "Passengers": {"Hotel": [{"last_name": "kane", "first_name": "mike"}], "Cruise": [{"gender": "M", "last_name": "Davis", "birth_date": "1963-04-07", "first_name": "Arthur", "citizenship": "US"}], "Driver": [{"age": "30-69", "last_name": "kane", "birth_date": "1973-04-07", "first_name": "mike"}], "Flight": [{"id": null, "email": null, "seats": null, "codeAs": null, "gender": "M", "user_id": null, "last_name": "Davis", "assistance": null, "birth_date": "1963-04-07", "first_name": "Arthur", "middle_name": "", "nationality": "US", "passport_id": null, "passport_valid_date": null}], "Attraction": [{"last_name": "kane", "first_name": "mike", "language_service": "US"}]}, "HotelRequest": {"productGid": "cdd82f2616f600f71a68e9399c51276e"}, "CruiseRequest": {"productGid": "cdd82f2616f600f71a68e9399c51276e"}, "DriverRequest": {"productGid": "cdd82f2616f600f71a68e9399c51276e"}, "FlightRequest": {"pnr": null, "uid": "OE96040", "email": "mike.kane@techork.com", "alipay": false, "is_b2b": false, "marker": null, "uplift": false, "productGid": "c6ae37ae73380c773cadf28fc0af9db2", "delay_change": false, "user_country": "us", "is_facilitate": 0, "user_language": "en-US", "insurance_code": "P7", "currency_symbol": "$", "user_time_format": "h:mm a", "client_ip_address": "92.115.180.30", "trip_protection_amount": "0", "user_month_date_format": {"long": "EEE MMM d", "short": "MMM d", "fullDateLong": "EEE MMM d", "fullDateShort": "MMM d, YYYY"}}, "AuxiliarProducts": {"Hotel": [], "Cruise": [], "Driver": [], "Flight": {"basket": {"1c3df555-a2dc-4813-a055-2a8bf56fd8f1": {"price": {"base": {"amount": 2000, "currency": "USD", "decimal_places": 2, "in_original_currency": {"amount": 1820, "currency": "USD", "decimal_places": 2}}, "fees": [], "taxes": [{"amount": 200, "currency": "USD", "tax_type": "tax", "decimal_places": 2, "in_original_currency": {"amount": 182, "currency": "USD", "decimal_places": 2}}], "total": {"amount": 2400, "currency": "USD", "decimal_places": 2, "in_original_currency": {"amount": 2184, "currency": "USD", "decimal_places": 2}}, "markups": [{"amount": 600, "currency": "USD", "markup_type": "markup", "decimal_places": 2, "in_original_currency": {"amount": 546, "currency": "USD", "decimal_places": 2}}]}, "benefits": [], "quantity": 1, "validity": {"state": "valid", "valid_to": "2020-05-22T16:49:08Z", "valid_from": "2020-05-22T16:34:08Z"}, "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252", "product_id": "741bcc97-c2fe-4820-b14d-f11f32e6fadb", "display_name": "10kg Bag", "product_type": "bag", "basket_item_id": "1c3df555-a2dc-4813-a055-2a8bf56fd8f1", "product_details": {"size": 150, "weight": 10, "size_unit": "cm", "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba", "weight_unit": "kg", "passenger_id": "p1"}}, "2654f3f9-8990-4d2e-bdea-3b341ad5d1de": {"price": {"base": {"amount": 2000, "currency": "USD", "decimal_places": 2, "in_original_currency": {"amount": 1820, "currency": "USD", "decimal_places": 2}}, "fees": [], "taxes": [{"amount": 200, "currency": "USD", "tax_type": "tax", "decimal_places": 2, "in_original_currency": []}], "total": {"amount": 2600, "currency": "USD", "decimal_places": 2, "in_original_currency": {"amount": 2366, "currency": "USD", "decimal_places": 2}}, "markups": [{"amount": 400, "currency": "USD", "markup_type": "markup", "decimal_places": 2, "in_original_currency": {"amount": 364, "currency": "USD", "decimal_places": 2}}]}, "benefits": [], "quantity": 1, "validity": {"state": "valid", "valid_to": "2020-05-22T16:49:08Z", "valid_from": "2020-05-22T16:34:08Z"}, "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252", "product_id": "a17e10ca-0c9a-4691-9922-d664a3b52382", "display_name": "Seat 15C", "product_type": "seat", "basket_item_id": "2654f3f9-8990-4d2e-bdea-3b341ad5d1de", "product_details": {"row": 15, "column": "C", "segment_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba", "passenger_id": "p1"}}, "5d5e1bce-4577-4118-abcb-155823d8b4a3": [], "6acd57ba-ccb7-4e86-85e7-b3e586caeae2": [], "dffac4ba-73b9-4b1b-9334-001817fff0cf": [], "e960eff9-7628-4645-99d8-20a6e22f6419": []}, "orders": [], "country": "US", "tickets": [{"state": "in_basket", "ticket_id": "8c1c9fc8-d968-4733-93a8-6067bac2543f", "journey_ids": ["aab8980e-b263-4624-ad40-d6e5e364b4e9"], "basket_item_ids": ["dffac4ba-73b9-4b1b-9334-001817fff0cf", "e960eff9-7628-4645-99d8-20a6e22f6419", "6acd57ba-ccb7-4e86-85e7-b3e586caeae2", "5d5e1bce-4577-4118-abcb-155823d8b4a3"], "ticket_basket_item_id": "dffac4ba-73b9-4b1b-9334-001817fff0cf"}, {"state": "offered", "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252", "journey_ids": ["1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"], "offered_price": {"total": 20000, "currency": "USD", "decimal_places": 2}, "basket_item_ids": ["2654f3f9-8990-4d2e-bdea-3b341ad5d1de", "1c3df555-a2dc-4813-a055-2a8bf56fd8f1"]}], "trip_id": "23259b86-3208-44c9-85cc-4b116a822bff", "currency": "USD", "journeys": [{"segments": [{"fare_basis": "OTZ0RO/Y", "fare_class": "O", "segment_id": "938d8e82-dd7c-4d85-8ab4-38fea8753f6f", "fare_family": "Basic Economy", "arrival_time": "2020-07-07T22:30:00Z", "departure_time": "2020-07-07T21:10:00Z", "arrival_airport": "LHR", "number_of_stops": 0, "departure_airport": "EDI", "marketing_airline": "BA", "operating_airline": "BA", "marketing_flight_number": "1465", "operating_flight_number": "1465"}], "journey_id": "aab8980e-b263-4624-ad40-d6e5e364b4e9"}, {"segments": [{"fare_basis": "NALZ0KO/Y", "fare_class": "N", "segment_id": "7d693cb0-d6d8-49f0-9489-866b3d789215", "fare_family": "Basic Economy", "arrival_time": "2020-07-14T08:35:00Z", "departure_time": "2020-07-14T07:05:00Z", "arrival_airport": "EDI", "number_of_stops": 0, "departure_airport": "LGW", "marketing_airline": "BA", "operating_airline": "BA", "marketing_flight_number": "2500", "operating_flight_number": "2500"}], "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"}], "language": "en-US", "passengers": [{"surname": "Van Gogh", "first_names": "Vincent Willem", "passenger_id": "ee850c82-e150-4f35-b0c7-228064c2964b"}], "trip_state_hash": "69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee", "trip_access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"}, "Attraction": []}, "totalOrderAmount": 821.49, "AttractionRequest": {"productGid": "cdd82f2616f600f71a68e9399c51276e"}}, "payment": {"date": "2021-03-20", "type": "CARD", "amount": 821.49, "currency": "USD", "transactionId": 1234567890}, "offerGid": "73c8bf13111feff52794883446461740", "productQuotes": [{"gid": "7e09fe0615dfa79fd9be940afadbc281"}]}', true));
//        VarDumper::dump( $t['Request']['FlightRequest']['uid']);
//        die;



        die;
//        $order->processing();
        $order->cancel('', null, 295);
        $repo->save($order);


        die;
//        $hotelQuote = HotelQuote::findOne(31);
//        $bookService = \Yii::$container->get(HotelQuoteBookService::class);
//        $bookService->book($hotelQuote);
//        die;
        \Yii::$app->queue_job->push(new BookingHotelJob(34));
        die;
        $productQuoteRepository = \Yii::createObject(ProductQuoteRepository::class);
        $quote = $productQuoteRepository->find(109);
        $quote->booked();
        $productQuoteRepository->save($quote);
        die;


//        $eventDispatcher = \Yii::createObject(EventDispatcher::class);
//        $eventDispatcher->dispatch(new ProductQuoteBookedEvent(109, null, null, null, null));
//        $eventDispatcher->dispatch(new QuoteBookedEvent(110));
//        \Yii::$app->queue_job->push(new StartBookingJob(9));
//        die;
        $repo = \Yii::createObject(OrderProcessManagerRepository::class);



        $process = OrderProcessManager::create(10, new \DateTimeImmutable());
        $repo->save($process);
        die;
        $process = OrderProcessManager::findOne(9);
        $process->bookingOtherProducts(new \DateTimeImmutable());
        $repo->save($process);

        die;
        $data = [
            'id' => '5c23460e-6fc1-4ea6-a368-df52ca5b293e',
            'rid' => 'b1ee59aa-5315-4714-88ee-4487f7ccca31',
            'token' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
            'visitor' => [
                'id' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
                'username' => 'guest-1100',
                'phone' => null,
                'token' => '86f8680b-5c6c-4c57-89ea-cfdb768ef63d',
            ],
            'agent' => [
                'name' => 'bot',
                'username' => 'bot',
                'email' => 'bot@techork.com',
            ],
            'msg' => 'Open-source',
            'timestamp' => 1601728481909,
            'u' => [
                '_id' => 'mBhSE9dtz5i5shAdH',
                'username' => 'guest-1100',
            ],
        ];
        $form = (new ClientChatRequestApiForm())->fillIn('GUEST_UTTERED', $data);

        $clientChatRepository = \Yii::createObject(ClientChatRepository::class);
        $clientChat = $clientChatRepository->findByRid($form->data['rid'] ?? '');
        $message = ClientChatMessage::createByApi($form, (new ClientChatRequest())->ccm_event ?? 0, ClientChatPlatform::getDefaultPlatform());

        $tr = \Yii::$app->get('db_postgres')->beginTransaction();
        try {
            $clientChatMessageRepository = \Yii::createObject(ClientChatMessageRepository::class);
            $clientChatMessageRepository->save($message, 0);

            $tr->commit();
        } catch (\Throwable $e) {
            $tr->rollBack();
        }
        die;




        $chat = ClientChat::findOne(9);
        $chat->cch_status_id = ClientChat::STATUS_TRANSFER;
        $chat->assignOwner(167);
        $repo = \Yii::createObject(ClientChatRepository::class);
        $repo->save($chat);
        die;

        $service = \Yii::createObject(ClientChatMessageService::class);
        VarDumper::dump($service->increaseUnreadMessages(9, 295));
        die;


        $unread = ClientChatUnread::find()->select(['*', 'cch_owner_user_id as ownerId'])->andWhere(['ccu_cc_id' => 8])->innerJoinWith('chat', false)->one();
        VarDumper::dump($unread);
        die;

        $conferenceSid  = 'CF2e8a4d3fbbd53c399f5819b55f369fed';
        $eventsLog = ConferenceEventLog::find()
            ->select(['cel_event_type', 'cel_data'])
            ->andWhere(['cel_conference_sid' => $conferenceSid])->orderBy(['cel_sequence_number' => SORT_ASC])->asArray()
            //->limit(10)
            ->all();
        $events = [];
        foreach ($eventsLog as $item) {
            $events[] = EventFactory::create($item['cel_event_type'], $item['cel_data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
//        VarDumper::dump($aggregate->getParticipantsResult());
        $printer = new HtmlFormatter($aggregate->logs);
        VarDumper::dump($printer->format());
    }

    public function actionTestIsChatTransfer(int $cchId)
    {
        $chatRepository = \Yii::createObject(ClientChatRepository::class);
        var_dump($chatRepository->isChatInTransfer($cchId));
    }

    public function actionTestChatVisitorDataRules()
    {
        $faker = Factory::create();
        $data = new ClientChatVisitorData();
        $data->cvd_referrer = $faker->realText(1005, 1);

        var_dump(strlen($data->cvd_referrer));
        var_dump($data->validate());
        var_dump($data->errors);
    }

    public function actionTestUserAccessToAllChats(int $userId)
    {
        $channelIds = [1,2,3,4,5,6,7,8];
        $service = \Yii::createObject(ClientChatUserAccessService::class);
        $service->setUserAccessToAllChatsByChannelIds($channelIds, $userId);
    }

    public function actionVirtualCronTest()
    {
        echo date('Y-m-d H:i:s');
        \Yii::info($_SERVER, 'info\TestController:actionTest');
        return ExitCode::OK;
    }

    public function actionFillChatCannedTablesWithTestData()
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processedCategory = 0;
        $timeStart = microtime(true);


//        $categoryCounts = 10000 * 2;
//        Console::startProgress(0, $categoryCounts, 'Counting categories: ', false);
//        $faker = Factory::create('ru_RU');
//        for ($i = 1; $i <= $categoryCounts; $i++) {
//            $cannedResponseCategory = new ClientChatCannedResponseCategory();
//            $cannedResponseCategory->crc_name = $faker->sentence(3, true);
//            $cannedResponseCategory->crc_enabled = 1;
//            $cannedResponseCategory->crc_created_dt = date('Y-m-d H:i:s');
//            $cannedResponseCategory->crc_updated_dt = date('Y-m-d H:i:s');
//            $cannedResponseCategory->save();
//
//            Console::updateProgress($i, $categoryCounts);
//        }
//        Console::endProgress("Categories created." . PHP_EOL);


        $cannedResponseCnt = 10000 * 2;
        Console::startProgress(0, $cannedResponseCnt, 'Counting canned responses: ', false);
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= $cannedResponseCnt; $i++) {
            $cannedResponse = new ClientChatCannedResponse();
            $cannedResponse->cr_language_id = 'ru-RU';
            $cannedResponse->cr_user_id = 464;
            $cannedResponse->cr_sort_order = $i;
            $cannedResponse->cr_message = $faker->realText(1000);
            $cannedResponse->cr_created_dt = date('Y-m-d H:i:s');
            $cannedResponse->cr_updated_dt = date('Y-m-d H:i:s');
            $cannedResponse->save();

            Console::updateProgress($i, $cannedResponseCnt);
        }
        Console::endProgress("Canned responses created." . PHP_EOL);

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);

        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g ProcessedCategory: %w[' . $processedCategory . '] %g %n'), PHP_EOL;

        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }


    public function actionTestLead($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);

        if (!$lead) {
            echo Console::renderColoredString('%r --- Error: Lead not found by id ' . $leadId . '   %r' . ' %n', true), PHP_EOL;
        }

        $additionalInfo = $lead->getAdditionalInformationFormFirstElement();

        $additionalInfo->vtf_processed = true;
        $additionalInfo->tkt_processed = false;
        $additionalInfo->exp_processed = true;

        $lead->additional_information = Json::encode(ArrayHelper::toArray($additionalInfo));

        if (!$lead->save()) {
            echo Console::renderColoredString('%r --- Error: Lead not saved %r' . ' %n', true), PHP_EOL;
        }

        $lead->sendNotifOnProcessingStatusChanged();
    }

    public function actionShowChatProjectConfigDto($projectId)
    {
        $config = ClientChatProjectConfig::findOne(['ccpc_project_id' => (int)$projectId]);
        if (!$config) {
            echo Console::renderColoredString('%r --- Error: project config not found %r' . ' %n', true), PHP_EOL;
            exit;
        }

        $dto = new ProjectConfigApiResponseDto($config);
        echo $dto->endpoint . PHP_EOL;
    }

    /**
     * @throws \Exception
     */
    public function actionTestCentrifugo()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $n = 0;
        while (true) {
            $n++;
            $calls = Call::find()
                ->where(['c_status_id' => [Call::STATUS_IVR, Call::STATUS_QUEUE, Call::STATUS_COMPLETED]])
                ->andWhere('c_id > FLOOR(RAND()*(3368194-100000)+100000)')
                //->orderBy('RAND()')
                ->limit(rand(1, 2))->all(); //['c_id' => SORT_DESC]
            foreach ($calls as $call) {
                $call->c_status_id = random_int(1, 12);
                $call->c_source_type_id = random_int(1, 12);
                $call->c_call_type_id = random_int(1, 4);
                $call->c_is_new = random_int(0, 1);
                $call->c_is_transfer = (int)!$call->c_is_new;
                $call->c_updated_dt = null; //date("Y-m-d H:i:s", strtotime('-' . random_int(1, 60) . ' minutes'));
                $call->c_created_dt = $call->c_updated_dt;
                $call->c_queue_start_dt = date("Y-m-d H:i:s", strtotime('-' . random_int(1, 15) . ' minutes'));
                $call->sendFrontendData('update');
                echo ' - ' . $call->c_id . PHP_EOL;
            }
            usleep(0.9 * 1000000);
            if ($n > 100000) {
                break;
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * @throws \Exception
     */
    public function actionTestCentrifugo2()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $n = 0;
        while (true) {
            $n++;
            $acessList = CallUserAccess::find()->limit(5)->orderBy('RAND()')->all();
            foreach ($acessList as $item) {
                $item->cua_call_id = 3368193;
                //$item->cua_user_id = 188;
                $item->sendFrontendData('insert');

                VarDumper::dump($item->attributes);
            }
            usleep(0.1 * 1000000);
            if ($n > 100000) {
                break;
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @throws \Exception
     */
    public function actionUserOnline()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $n = 0;

        $users = [340, 635, 615, 600, 595];

        while (true) {
            $n++;
            $userList = UserOnline::find()->limit(5)->orderBy('RAND()')->all();
            foreach ($userList as $item) {
                //$item->uo_user_id = Employee::find()->select('id')->limit(1)->orderBy('RAND()')->scalar();
                $item->uo_user_id = $users[random_int(0, 4)];
                //$item->cua_user_id = 188;
                $item->uo_idle_state = (bool) random_int(0, 1);
                $item->sendFrontendData('insert');

                VarDumper::dump($item->attributes);
            }
            usleep(1 * 1000000);
            if ($n > 100000) {
                break;
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @throws \Exception
     */
    public function actionTestCentrifugoCall($id)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $call = Call::find()
            ->where(['c_id' => $id])
            ->one(); //['c_id' => SORT_DESC]
        if ($call) {
            $call->c_status_id = Call::STATUS_COMPLETED;
            $call->sendFrontendData('update');
            echo ' - ' . $call->c_id . PHP_EOL;
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
    public function actionTestJob()
    {
        \Yii::$app->queue_job->push(new BookingFlightJob(2));
    }

    public function actionTestTranzaction()
    {
        $command = new Command(null, null, 1, '', [], 1, '', '', '', '');
        $handler = \Yii::createObject(Handler::class);
        $handler->handle($command);
    }

    public function actionTestLogExecutionTime()
    {
        $logExecutionTime = new LogExecutionTime();
        $logExecutionTime->start('step1');
        sleep(2);
        $logExecutionTime->end()->start('step2');
        sleep(3);
        $logExecutionTime->end();
        $logExecutionTime->start('step3');
        $logExecutionTime->end();
        $logExecutionTime->start('step4');
        sleep(1);
        $logExecutionTime->end()->start('step5');
        sleep(5);
        $logExecutionTime->end();

        var_dump($logExecutionTime->getResult());
    }

    public function actionTestPhoneNumberRedialQuery()
    {
        $phone = PhoneNumberRedialQuery::getOneMatchingByClientPhone('+185553464564', 8);
        var_dump($phone->phoneList->pl_phone_number ?? null);
        die;
    }

    public function actionNotif()
    {
//        $notification = Notifications::create(
//            464,
//            'Call - Long Queue Time',
//            'Call ID:' . 15646852 . ' to PRICELINE Sales from +37378*****456 is stuck in the queue for 15 sec.',
//            Notifications::TYPE_WARNING,
//            true
//        );
//        if ($notification) {
//            Notifications::publish('getNewNotification', ['user_id' => 464], NotificationMessage::add($notification));
//        }
        $message = 'Call ID:' . 15646852 . ' to PRICELINE Sales from +37378*****456 is stuck in the queue for 15 sec.';
        Notifications::publish(
            'showDesktopNotification',
            ['user_id' => 464],
            NotificationMessage::desktopMessage(
                464 . '-desk',
                'Call - Long Queue Time',
                '<strong>Hello World</strong> Hello World',
                'info',
                $message,
                true
            )
        );
    }

    public function actionFirstLeadFlow()
    {
        $firstLeadFlow = LeadFlowQuery::getFirstOwnerOfLead(513131);
        var_dump($firstLeadFlow->toArray());
        die;
    }

    public function actionBlameable()
    {
        echo 'Blameable ' . Auth::employeeId();
        die;
    }
}
