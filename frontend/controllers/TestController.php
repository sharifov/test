<?php

namespace frontend\controllers;

use common\components\antispam\CallAntiSpamDto;
use common\components\CommunicationService;
use common\components\ga\GaLead;
use common\components\ga\GaQuote;
use common\components\jobs\CallPriceJob;
use common\components\jobs\UserTaskCompletionJob;
use common\components\Metrics;
use common\components\jobs\TelegramSendMessageJob;
use common\models\Call;
use common\models\Client;
use common\models\CreditCard;
use common\models\Currency;
use common\models\CurrencyHistory;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\Quote;
use common\models\QuoteStatusLog;
use common\models\UserProfile;
use common\models\UserProjectParams;
use DateInterval;
use DatePeriod;
use DateTime;
use frontend\helpers\JsonHelper;
use frontend\models\CommunicationForm;
use frontend\models\form\CreditCardForm;
use kivork\FeatureFlag\Models\flags\dateTime\DateTimeFeatureFlag;
use kivork\FeatureFlag\Models\flags\dateTime\DateTimeFeatureFlagDTO;
use modules\attraction\models\AttractionQuote;
use modules\attraction\src\services\AttractionQuotePdfService;
use modules\cases\src\abac\saleList\SaleListAbacObject;
use modules\eventManager\src\EventApp;
use modules\featureFlag\FFlag;
use modules\featureFlag\models\debug\DebugFeatureFlag;
use modules\featureFlag\models\debug\DebugFeatureFlagDTO;
use modules\featureFlag\models\user\UserFeatureFlag;
use modules\featureFlag\models\user\UserFeatureFlagDTO;
use modules\flight\models\FlightQuote;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use modules\flight\src\services\flightQuote\FlightQuoteTicketIssuedService;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\services\hotelQuote\HotelQuotePdfService;
use modules\lead\src\abac\LeadSearchAbacObject;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacDto;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacObject;
use modules\lead\src\entities\lead\LeadQuery;
use modules\lead\src\events\LeadEvents;
use modules\lead\src\services\LeadTaskListService;
use modules\order\src\abac\OrderAbacObject;
use modules\order\src\entities\order\Order;
use modules\order\src\services\OrderPdfService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\services\RentCarQuotePdfService;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\TaskObject;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\entity\UserActivity;
use modules\user\userActivity\service\UserActivityService;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventDataService;
use modules\webEngage\src\service\WebEngageRequestService;
use Mpdf\Tag\P;
use PhpOffice\PhpSpreadsheet\Shared\TimeZone;
use src\access\CallAccess;
use src\auth\Auth;
use src\dispatchers\DeferredEventDispatcher;
use src\entities\cases\Cases;
use src\repositories\NotFoundException;
use src\services\cases\CasesCommunicationService;
use src\helpers\app\AppHelper;
use src\helpers\call\CallHelper;
use src\helpers\payment\CreditCardHelper;
use src\model\conference\entity\aggregate\ConferenceLogAggregate;
use src\model\conference\entity\aggregate\log\HtmlFormatter;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLogQuery;
use src\model\conference\entity\conferenceEventLog\EventFactory;
use src\model\conference\useCase\PrepareCurrentCallsForNewCall;
use src\model\conference\useCase\saveParticipantStats\Command;
use src\model\notification\events\NotificationEvents;
use src\repositories\cases\CasesSaleRepository;
use src\services\cases\CasesSaleService;
use src\services\client\ClientManageService;
use src\services\clientChatService\ClientChatService;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;
use src\services\TransactionManager;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\base\Event;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use common\components\ReceiveEmailsJob;
use yii\queue\Queue;
use yii\web\NotFoundHttpException;

/**
 * Test controller
 * @property ClientManageService $clientManageService
 * @property DeferredEventDispatcher $dispatcher
 * @property TransactionManager $transactionManager
 */
class TestController extends FController
{
    private $repository;
    private $dispatcher;
    private $transactionManager;
    private $clientManageService;
    private $casesSaleService;

    public function __construct(
        $id,
        $module,
        ClientManageService $clientManageService,
        DeferredEventDispatcher $dispatcher,
        TransactionManager $transactionManager,
        CasesSaleService $casesSaleService,
        $config = []
    ) {
        $this->clientManageService = $clientManageService;
        $this->dispatcher = $dispatcher;
        $this->transactionManager = $transactionManager;
        $this->casesSaleService = $casesSaleService;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => [
                            Employee::ROLE_ADMIN,
                            Employee::ROLE_AGENT,
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'GET'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    public function actionHolibob()
    {
        $graphqlEndpoint = 'https://api.sandbox.holibob.tech/graphql';
        $apiKey = 'a22a880b-4b40-4023-b93d-49ea130c15d4';
        $secret = 'f787e25040b65e205b7d57992a7d9d183784811b';

        $dt = new DateTime();
        $date = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
        //$date = '2021-02-26T06:25:38.653Z';

        $query = '{"query":"query {welcome}"}';

        $string = $date . $apiKey . 'POST/graphql' . $query;

        $base64HashSignature = base64_encode(hash_hmac('sha1', $string, $secret, true));

        /*$hexHash = hash_hmac('sha1', utf8_encode($string), utf8_encode($secret));
        $base64HashSignature = base64_encode(hex2bin($hexHash));*/
        //var_dump($base64HashSignature); die();

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $graphqlEndpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $apiKey,
                'x-holibob-date' => $date,
                'x-holibob-signature' => $base64HashSignature,
            ],
            'json' => $query,
        ]);

        $json = $response->getBody()->getContents();
        $body = json_decode($json);
        $data = $body->data;
        print_r($data);
    }

    public function actionHolibob2()
    {
        $query = '{"query":"query {welcome}"}';

        $graphqlEndpoint = 'https://api.sandbox.holibob.tech/graphql';
        $apiKey = 'a22a880b-4b40-4023-b93d-49ea130c15d4';
        $secret = 'f787e25040b65e205b7d57992a7d9d183784811b';

        $dt = new DateTime();
        $date = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';

        $string = $date . $apiKey . 'POST/graphql' . $query;
        $base64HashSignature = base64_encode(hash_hmac('sha1', $string, $secret, true));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $graphqlEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        //$headers[] = "Content-Type: application/json";
        $headers[] = "x-api-key: $apiKey";
        $headers[] = "x-holibob-date: $date";
        $headers[] = "x-holibob-signature: $base64HashSignature";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        print_r($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
    }

    public function actionTest()
    {
        $command = new Command('CF17c1116022347e5202ef035e2e88286f', 4973);
        $handler = Yii::createObject(\src\model\conference\useCase\saveParticipantStats\Handler::class);
        $handler->handle($command);

        die;
        $conferenceSid  = 'CF598673a88ea9deb25aeb04b2821fe24d';
        $eventsLog = ConferenceEventLogQuery::getRawData($conferenceSid);

        $events = [];
        foreach ($eventsLog as $item) {
            $events[] = EventFactory::create($item['type'], $item['data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
        $printer = new HtmlFormatter($aggregate->logs);
        return $this->renderContent($printer->format());

        return '';



//        $userId = 294;
//        $calls = Call::find()->andWhere([
//            'c_created_user_id' => $userId,
//            'c_call_type_id' => [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT],
//            'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]
//        ])
//            ->innerJoinWith(['conferenceParticipants' => static function(ConferenceParticipantQuery $query) {
//                $query->andOnCondition([
//                    'cp_type_id' => ConferenceParticipant::TYPE_USER,
//                ]);
//                $query->andOnCondition(['IS NOT', 'cp_status_id', null]);
//                $query->andOnCondition(['<>', 'cp_status_id', ConferenceParticipant::STATUS_LEAVE]);
//            }], false)
//            ->innerJoin(Conference::tableName(), 'cf_id = c_conference_id')
//            ->all();
//
//        VarDumper::dump($calls);
//        die;

//        VarDumper::dump($r2);
//
        die;

//        $job = new SmsPriceJob();
//        $job->smsSid = 'SMbf59aed897c2b059a9fcb995746f5b9c';
//        Yii::$app->queue_job->push($job);

        $job = new CallPriceJob();
        $job->callSid = 'CA14e5d6623df4a3d428a95117bd175359';
        Yii::$app->queue_job->push($job);

//        $job = new CallPriceJob();
//        $job->callSid = 'CAd6a42a1e827f8ef00cec041d6c6bc8b3';
//        Yii::$app->queue_job->push($job);

        die;
        VarDumper::dump(Json::decode('{
    "message": {
        "rid": "f93a9c3e-e04a-4e0f-b39e-5be30f938da4",
        "attachments": [
            {
                "image_url": "https://ichef.bbci.co.uk/news/1024/branded_news/12A9B/production/_111434467_gettyimages-1143489763.jpg",
                "title": "Title",
                "actions": [
                    {
                        "type": "button",
                        "msg_in_chat_window": true,
                        "text": "button 1",
                        "msg": "/payload"
                    },
                    {
                        "type": "button",
                        "msg_in_chat_window": true,
                        "text": "button 2",
                        "msg": "/payload"
                    }
                ],
                "fields": [
                    {
                        "short": true,
                        "title": "Test",
                        "value": "Testing out something or other"
                    },
                    {
                        "short": true,
                        "title": "Another Test",
                        "value": "[Link](https://google.com/) something and this and that."
                    }
                ]
            },
            {
                "image_url": "https://ichef.bbci.co.uk/news/1024/cpsprodpb/83D7/production/_111515733_gettyimages-1208779325.jpg",
                "title": "Title 2"
            }
        ],
        "customTemplate": "carousel"
    }
}'));
        die;

//        Notifications::publish(HoldMessage::COMMAND, ['user_id' => 295], [
//            'data' => [
//                'command' => HoldMessage::COMMAND_UNHOLD,
//                'call' => [
//                    'sid' => 'sid12',
//                ],
//            ],
//        ]);
//        die;
//
//
//         $callInfo = [
//            'data' => [
//                'call' => [
//                    'id' => 5,
//                ],
//            ],
//        ];
//        Notifications::publish('removeIncomingRequest', ['user_id' => 295], $callInfo);
//        die;
//
        $tmp = 2;
        $callInfo = [

            'typeId' => 1,
            'type' => 'Inc ' . $tmp,
            'callId' => $tmp,
            'callSid' => 'sid' . $tmp,
            'fromInternal' => false,
            'project' => 'hop',
            'source' => 'Source new ',
//            'status' => 'Ringing',
            'status' => 'In progress',
//            'status' => 'Completed',
//            'status' => 'Hold',
            'isListen' => false,
            'isCoach' => false,
            'isBarge' => false,
            'isMute' => false,
            'isHold' => false,
            'contact' => [
                'name' => 'Name ' . $tmp,
                'company' => 'Company ' . $tmp,
                'phone' => '+00 ' . $tmp
            ],
            'department' => 'Sales',
//            'queue' => 'general',
//            'queue' => 'hold',
            'queue' => 'inProgress',
            'duration' => 3595,
            'conference' => [
                    'sid' => 'conf' . $tmp,
                    'duration' => 0,
                    'participants' => [
                        [
                            'callSid' => 'callSid1',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 1
                        ],
                        [
                            'callSid' => 'callSid2',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 2
                        ],
                        [
                            'callSid' => 'callSid3',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 3
                        ],
                        [
                            'callSid' => 'callSid4',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 4
                        ],
                        [
                            'callSid' => 'callSid5',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 5
                        ],
                        [
                            'callSid' => 'callSid6',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 6
                        ],
                        [
                            'callSid' => 'callSid7',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 7
                        ],
                        [
                            'callSid' => 'callSid8',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 8
                        ],
                        [
                            'callSid' => 'callSid9',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 9
                        ],
                        [
                            'callSid' => 'callSid10',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 10
                        ],
                        [
                            'callSid' => 'callSid11',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 11
                        ],
                        [
                            'callSid' => 'callSid12',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 12
                        ],
                    ],
            ]
        ];
//        Notifications::publish('updateIncomingCall', ['user_id' => 295], $callInfo);
//        Notifications::publish('callUpdate', ['user_id' => 295], $callInfo);
//        Notifications::publish('callUpdate', ['user_id' => 295], $callInfo);
//        die;

        $confData = [
            'data' => [
                'command' => 'conferenceUpdate',
                'call' => [
                    'sid' => 'sid2',
                ],
                'conference' => [
                    'sid' => 'conf' . $tmp,
                    'duration' => 0,
                    'participants' => [
                        [
                            'callSid' => 'sid2',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 1
                        ],

                        [
                            'callSid' => 'callSid5',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 5
                        ],
                        [
                            'callSid' => 'callSid6',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 6
                        ],
                        [
                            'callSid' => 'callSid7',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 7
                        ],
                        [
                            'callSid' => 'callSid8',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 8
                        ],
                        [
                            'callSid' => 'callSid9',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 9
                        ],
                        [
                            'callSid' => 'callSid10',
                            'avatar' => 'N',
                            'name' => 'Name 1',
                            'phone' => '+373 1',
                            'type' => 'coaching',
                            'duration' => 10
                        ],
                        [
                            'callSid' => 'callSid11',
                            'avatar' => 'N',
                            'name' => 'Name 2',
                            'phone' => '+373 2',
                            'type' => '',
                            'duration' => 11
                        ],
                        [
                            'callSid' => 'callSid12',
                            'avatar' => 'N',
                            'name' => 'Name 3',
                            'phone' => '+373 3',
                            'type' => '',
                            'duration' => 12
                        ],
                    ]
                ],
            ],
        ];

        Notifications::publish('conferenceUpdate', ['user_id' => 295], $confData);
        die;

//
//        $tmp = 101;
//        Notifications::publish('callUpdate', ['user_id' => 295],
//            [
//                'callId' => $tmp,
//                'status' => 'In progress',
//                'duration' => 60,
//                'snr' => 1,
//                'leadId' => 1,
//                'typeId' => 1,
//                'type' => 'Outgoing',
//                'source_type_id' => '',
//                'phone' => '+373 ' . $tmp,
//                'name' => 'Name ' . $tmp,
//                'fromInternal' => false,
//                'isHold' => false,
//                'isListen' => false,
//                'isMute' => false,
//                'projectName' => 'Project ' . $tmp,
//                'sourceName' => 'Source ' . $tmp,
//                'isEnded' => false,
//                'contact' => [
//                    'name' => 'Xqwewe'
//                ]
//            ]
//        );
//        die;



        $callInfo = [
            'data' => [
                'call' => [
                    'sid' => 'sid1',
                ],
            ],
        ];
        Notifications::publish('removeIncomingRequest', ['user_id' => 295], $callInfo);

//        die;
//
//
//
//        die;
//
//        if ($call = Call::findOne(3371922)) {
//            VarDumper::dump($call->currentParticipant);
//        }
//
//        die;
//        $service = Yii::$app->comms;
//
//        $call = Call::findOne(['c_call_sid' => 'CA78c6d347bc1db1e33550997bb9b0b6c2']);
        ////
        ////        $service->disconnectFromConferenceCall($call->c_conference_sid, $call->c_call_sid);
        ////

        ////
        ////        $search = new ContactsSearch(295);
        ////        $search->searchContactsByWidget('373');
        die;


//        return $this->render('blank');
    }

    public function actionTestNew()
    {
        $userId = 295;
        $prepare = new PrepareCurrentCallsForNewCall($userId);
        $prepare->prepare();
        die;
    }

    private function getPathForTable($actions, $controller, &$batchTmpTableItem, &$batchTmpTableItemChild, $role)
    {
        if (!$actions) {
            return $actions;
        }
        foreach ($actions as $key => $action) {
            $str = '/' . Inflector::camel2id(strstr($controller, 'Controller', true)) . '/' . $action;
            $actions[$key] = $str;
            $batchTmpTableItem[$str] = 2;
            $batchTmpTableItemChild[] = [$str, $role];
        }
        return $actions;
    }

    public function actionEmail()
    {
        $swiftMailer = \Yii::$app->mailer;

        $mail = $swiftMailer
            ->compose()
            ->setTo(['chalpet@gmail.com' => 'Alex'])
            ->setFrom(['chalpet@gmail.com' => 'Dima'])
            ->setSubject('Test message');


        /*$headers = $mail->getSwiftMessage()->getHeaders();
        $headers->addTextHeader('Content-Transfer-Encoding','base64');*/

        $mail->setHeader('Message-ID', '123456.chalpet@gmail.com');
        $mail->setHtmlBody('HTML message');

        if ($mail->send()) {
            echo 'Send';
        } else {
            echo 'Not send';
        }
    }


    public function actionComPreview()
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;
        $data['origin'] = 'ORIGIN';
        $data['destination'] = 'DESTINATION';

        //$mailPreview = $communication->mailPreview(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $data, 'ru-RU');
        //$mailTypes = $communication->mailTypes(7);

        $content_data['email_body_html'] = '1';
        $content_data['email_body_text'] = '2';
        $content_data['email_subject'] = '3';
        $content_data['email_reply_to'] = 'chalpet-r@gmail.com';
        $content_data['email_cc'] = 'chalpet-cc@gmail.com';
        $content_data['email_bcc'] = 'chalpet-bcc@gmail.com';

        $mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

        /*if($mailSend['data']) {

        }*/






        VarDumper::dump($mailSend, 10, true);
    }

    public function actionSocket()
    {
        Notifications::create(Yii::$app->user->id, 'Test ' . date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);

        $socket = 'tcp://127.0.0.1:1234';
        $user_id = Yii::$app->user->id; //'tester01';
        $lead_id = 12345;
        $data['message'] = 'test ' . date('H:i:s');
        $data['command'] = 'getNewNotification';


        try {
            // соединяемся с локальным tcp-сервером
            $instance = stream_socket_client($socket);
            // отправляем сообщение
            if (fwrite($instance, json_encode(['user_id' => $user_id, /*'lead_id' => $lead_id,*/ 'multiple' => false, 'data' => $data]) . "\n")) {
                echo 'OK';
            } else {
                echo 'NO';
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        /*$host    = "localhost";
        $port    = 8080;

        $context = stream_context_create();

        $socket = stream_socket_client(
            $host . ':' . $port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        $key = $this->generateWebsocketKey();
        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Version: 13\r\n";
        $headers .= "Sec-WebSocket-Key: $key\r\n\r\n";
        stream_socket_sendto($socket, $headers);
        stream_socket_sendto($socket, 'this is my socket test to websocket');*/

        // echo 123;
        ///\yiicod\socketio\Broadcast::emit(CountEvent::name(), ['count' => 10]);
    }


    private function generateWebsocketKey()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';
        $key = '';
        $chars_length = strlen($chars);
        for ($i = 0; $i < 16; $i++) {
            $key .= $chars[mt_rand(0, $chars_length - 1)];
        }
        return base64_encode($key);
    }

    public function actionDetectLead()
    {
        $subject = 'RE Hello [lid:78456123]';
        $subject = 'RE Hello [uid:7asd845qwe6123]';
        $message_id = '<kiv.1.6.345.alex.connor@gmail.com> <qwewqeqweqwe.qweqwe@mail.com> <aasdfkjal.sfasldfkl@gmail.com> <kiv.12.63.348.alex.connor@gmail.com>';

        $matches = [];

        //preg_match('~\[lid:(\d+)\]~si', $subject, $matches);
        //preg_match('~\[uid:(\w+)\]~si', $subject, $matches);

        preg_match_all('~<kiv\.(.+)>~iU', $message_id, $matches);
        if (isset($matches[1]) && $matches[1]) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                if (isset($messageArr[2]) && $messageArr[2]) {
                    $lead_id = (int) $messageArr[2];

                    echo $lead_id . '<br>';
                }
            }
        }




        //VarDumper::dump($matches, 10, true);
    }

    public function actionLangPlural()
    {
        $n = random_int(0, 10);


        //echo Yii::t('app', 'You are the - {n,selectordinal,one{# один} two{# два} few{# мало} many{# несколько} other{# нет}} ', ['n' => $n]);

        Yii::$app->language = 'en-US';


        for ($i = 0; $i <= 20; $i++) {
            echo \Yii::t(
                'app',
                '{n, selectordinal,
     =0{У вас нет новых сообщений}

     one{У вас # непрочитанное сообщение}
     few{У вас # непрочитанных сообщения}
     many{У вас # непрочитанных сообщений...}
     other{У вас # прочитанных сообщений!}}',
                ['n' => $i]
            ) . '<br>';
        }
    }

    public function actionEmailJob()
    {
        $job = new ReceiveEmailsJob();

        $job->last_email_id = 1635;

        $data = [
            'last_email_id' => 1635,
            'run_all' => 'ok',
        ];

        $job->request_data = $data;

        /** @var Queue $queue */
        $queue = \Yii::$app->queue_email_job;

        return $queue->push($job);
    }

    public function actionCallTimer()
    {

        /*if ($vl->vl_call_status == 'initiated') {

        } elseif($vl->vl_call_status == 'ringing') {

        } elseif($vl->vl_call_status == 'in-progress') {

        } elseif($vl->vl_call_status == 'busy') {

        } elseif($vl->vl_call_status == 'completed') {
            $call->c_call_duration = $vl->vl_call_duration;
        }*/

        $statuses = ['initiated', 'ringing', 'in-progress', 'completed'];

        $statuses = ['ringing', 'in-progress', 'completed'];

        $statuses = ['ringing'];
        //$lead_id = 54719;

        $user_id = Yii::$app->user->id;

        $n = 0;
        foreach ($statuses as $status) {
            sleep(random_int(2, 3));
            $n++;
            // Notifications::socket($user_id, null, 'callUpdate', ['id' => 123, 'status' => $status, 'duration' =>  ($status == 'completed' ? random_int(51, 180) : 0), 'snr' => $n], true);
        }
    }


    public function actionIncomingCall()
    {


        /*if ($vl->vl_call_status == 'initiated') {

        } elseif($vl->vl_call_status == 'ringing') {

        } elseif($vl->vl_call_status == 'in-progress') {

        } elseif($vl->vl_call_status == 'busy') {

        } elseif($vl->vl_call_status == 'completed') {
            $call->c_call_duration = $vl->vl_call_duration;
        }*/

        $statuses = ['initiated', 'ringing', 'in-progress', 'completed'];
        $user_id = Yii::$app->user->id;
        $n = 0;


        $data = [];
        $data['client_name'] = 'Alexandr Test';
        $data['client_id'] = 345;
        $data['client_phone'] = '+3738956478';
        $data['last_lead_id'] = 34567;

        foreach ($statuses as $status) {
            sleep(random_int(3, 5));
            $data['status'] = $status;
            $n++;
            // Notifications::socket($user_id, $lead_id = null, 'incomingCall', $data, true);
            echo '<br>' . $status;
        }
    }


    public function actionQueryUser()
    {
        $user_id = Yii::$app->user->id;


        //$sqlRaw = $query->createCommand()->getRawSql();
        //$sqlRaw = $generalQuery->createCommand()->getRawSql();


        //echo $sqlRaw;

        //VarDumper::dump($sqlRaw, 10, true);
        //exit;

        //$users = Employee::getAgentsForCall($user_id, 8);

        $projects = Project::find()->all();
        foreach ($projects as $project) {
            VarDumper::dump($project->contactInfo->phone, 10, true);
        }


        //VarDumper::dump($users, 10, true);
    }

    /*
    public function actionLogin()
    {
        echo Yii::$app->user->id; exit;

        $user_id = Yii::$app->request->get('id');
        $user = Employee::findIdentity($user_id);
        if($user) {
            VarDumper::dump($user->attributes, 10, true);
            //exit;
            //Yii::$app->user->switchIdentity($user);

            Yii::$app->user->logout();
            if(!Yii::$app->user->login($user, 3600 * 24 * 30)) {
                echo 'Not logined'; exit;
            }



            //$this->redirect(['site/index']);
        }

        //Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);

        echo '--'.Yii::$app->user->id;

        echo Yii::$app->user->id;

        exit;

        //$this->redirect(['site/index']);
    }*/


    public function actionTz()
    {
        $offset = -5; //'+05:00';
        $timezoneName = timezone_name_from_abbr('', intval($offset) * 60 * 60, 0);
        //$timezoneName = timezone_name_from_abbr('', $offset,0);

        /*$date = new \DateTime(time(), new \DateTimeZone($timezoneName));
       // $clientTime = Yii::$app->formatter->asTime() $date->format('H:i');
        $clientTime = $date->format('H:i');



        $utcTime  = new \DateTime('now', new \DateTimeZone('UTC'));


        $gmtTimezone = new \DateTimeZone($timezoneName);
        $myDateTime = new \DateTime('2019-02-18 13:28', $gmtTimezone);





        $clientTime = $utcTime->format('H:i');*/



        //-----------------------------------------------------------


        $dt = new \DateTime();
        if ($timezoneName) {
            $timezone = new \DateTimeZone($timezoneName);
            $dt->setTimezone($timezone);
        }
        $clientTime =  $dt->format('H:i');

        echo $timezoneName . ' - ' . $dt->getOffset();
        //echo $clientTime;
    }

    public function actionTwml()
    {
        $twML = new VoiceResponse();
        $twML->say('Hello');
        $twML->play('https://api.twilio.com/cowbell.mp3', ['loop' => 5]);
        echo $twML;
    }

    public function actionTelegram()
    {
        $user_id = Yii::$app->user->id;

        if ($user_id) {
            $profile = UserProfile::find()->where(['up_user_id' => $user_id])->limit(1)->one();
            if ($profile && $profile->up_telegram && $profile->up_telegram_enable) {
                $tgm = Yii::$app->telegram;

                $tgm->sendMessage([
                    'chat_id' => $profile->up_telegram,
                    'text' => 'text 12345',
                ]);

                VarDumper::dump([
                    'chat_id' => $profile->up_telegram,
                ], 10, true);
            }
        }
    }

    public function actionJob()
    {
        $job = new TelegramSendMessageJob();
        $job->user_id = Yii::$app->user->id;
        $job->text = 'Test Job';

        $queue = Yii::$app->queue_job;
        $jobId = $queue->push($job);

        echo $jobId;
    }

    public function actionSettings()
    {
        VarDumper::dump(Yii::$app->params['settings'], 10, true);
    }


    public function actionTwml2()
    {
        $responseTwml = new VoiceResponse();
        $responseTwml->pause(['length' => 5]);
        /*$responseTwml->say('        Thank you for calling.  Your call is important to us.  Please hold while you are connected to the next available agent.', [
            'language' => 'en-US',
            'voice' => 'woman',
        ]);*/
        $responseTwml->play('https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3');
        $responseTwml->play('https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3');

        echo $responseTwml;
    }

    public function actionBlank()
    {
        return $this->render('blank');
    }

    public function actionTest2()
    {
        $call = new Call();
        $call->c_project_id = 6;
        $call->c_dep_id = 1;

        Employee::getUsersForCallQueue($call, 6);
    }

    public function actionNotify()
    {
        $host = \Yii::$app->params['url'] ?? '';
        // Notifications::socket(Yii::$app->user->id, null, 'openUrl', ['url' => $host . '/lead/view/b5d963c9241dd741e22b37d1fa80a9b6'], false);
    }

    public function actionNotify2()
    {
        Notifications::create(Yii::$app->user->id, 'Test ' . date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);
        //Notifications::socket(Yii::$app->user->id, null, 'openUrl', ['url' => $host . '/lead/view/b5d963c9241dd741e22b37d1fa80a9b6'], false);
    }
    public function actionNotify3()
    {
        /*$notification = new Notifications();
        $notification->n_title = 'New General Line Call';
        $notification->n_type_id = Notifications::TYPE_SUCCESS;
        $notification->n_user_id = 658;

        $notificationAbacDto = new NotificationAbacDto($notification);

        if (Yii::$app->abac->can($notificationAbacDto, NotificationAbacObject::OBJ_NOTIFICATION, NotificationAbacObject::ACTION_ACCESS)) {
            $message = 'New General Line Call';
            if ($ntf = Notifications::create($notification->n_user_id, 'New General Line Call', $message, Notifications::TYPE_SUCCESS, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $notification->n_user_id], $dataNotification);
            }
        }*/
    }

    public function actionTest3()
    {

       /* $a = [
            'lead' => [
                'sub_sources_code' => 'Q6R5L3',
        'adults' => '1',
        'cabin' => 'E',
        'emails' => [],
        'phones' => [
        0 => '+18885324041'
    ],
        'flights' => [
        0 => [
            'origin' => 'JFK',
                'destination' => 'MAD',
                'departure' => '06/29/2019'
            ],
            1 => [
        'origin' => 'MAD',
                'destination' => 'JFK',
                'departure' => '07/25/2019'
            ]
        ],
        'trip_type' => 'RT',
        'children' => '0',
        'infants' => 0,
        'uid' => 'W3ACBD0',
        'request_ip' => '188.131.53.1',
        'discount_id' => '2766',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
        'offset_gmt' => null,
        'user_language' => null,
    ]
];

        echo json_encode($a);

        exit;
        //VarDumper::dump($a, 10, true);*/

        //echo Lead2::findLastLeadByClientPhone('+3736959', 1, true);



        $call = Call::find()->where(['c_call_sid' => '123'])->limit(1)->one();
        if (!$call) {
            $call = new Call();
            $call->c_call_sid = uniqid();
            $call->c_from = '+373';
            $call->c_to = uniqid();
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_created_user_id = Yii::$app->user->id;
            $call->c_call_type_id = Call::CALL_TYPE_OUT;
        }

        /*if(!$call->c_lead_id && $lead_id) {
            $call->c_lead_id = (int) $lead_id;
        }

        if(!$call->c_project_id && $project_id) {
            $call->c_project_id = (int) $project_id;
        }

        $call->c_call_status = $call_status;*/
        //$call->c_updated_dt = date('Y-m-d H:i:s');

        if (!$call->save()) {
            $out['error'] = VarDumper::dumpAsString($call->errors);
            Yii::error($out['error'], 'PhoneController:actionAjaxSaveCall:Call:save');
        } else {
            $out['data'] = $call->attributes;
        }


        VarDumper::dump($out, 10, true);
        exit;


        return $this->render('blank');

        /*if (!$callsCount) {
            return false;
        }*/
    }

    public function actionCache()
    {

       /* $user_id = Yii::$app->user->id;

        $sql = \common\models\Notifications::find()->select('MAX(n_id)')->where(['n_user_id' => $user_id])->createCommand()->rawSql;
        //echo $sql; exit;


        $db = \Yii::$app->db;
        $duration = 0;
        $dependency = new DbDependency();
        $dependency->sql = $sql;

        $dependency = null;  // optional dependency


        $newCount = $db->cache(function ($db) use ($user_id) {
            return \common\models\Notifications::findNewCount($user_id);
        }, $duration, $dependency);

        $model = $db->cache(function ($db) use ($user_id) {
            return \common\models\Notifications::findNew($user_id);
        }, $duration, $dependency);

        VarDumper::dump($newCount, 10, true);
        //VarDumper::dump($model, 10, true);*/

        return $this->render('blank');
    }

    public function actionMysql()
    {
        $sqlData = [];
        $sqlData[] = "SELECT COUNT(*) FROM `call`";
        $sqlData[] = "SELECT * FROM `call` WHERE c_call_sid = 'CA4cd4b85d370ee0d517119e50da556b16'";
        $sqlData[] = "SELECT * FROM `call` WHERE c_call_sid = 'CAbba5c4a2d05fa83c35d5eb458d68eb39'";
        $sqlData[] = "SELECT * FROM `call` WHERE c_call_sid = 'CA95891e5bf4bb6d509dcd167bbd898142'";
        $sqlData[] = "SELECT COUNT(*) FROM `sms`";
        $sqlData[] = "SELECT COUNT(*) FROM `email`";
        $sqlData[] = "SELECT COUNT(*) FROM `leads`";
        $sqlData[] = "SELECT COUNT(*) FROM `quotes`";


        echo '<h2>SQL x 1</h2><table border="1" cellpadding="3" cellspacing="1">';
        foreach ($sqlData as $sql) {
            $time_start = microtime(true);
            $result = Yii::$app->db->createCommand($sql)->queryAll();
            $time_end = microtime(true);
            echo '<tr><td>' . $sql . '</td><td>Time: ' . round($time_end - $time_start, 6) . '</td></tr>';
        }
        echo '</table>';


        echo '<hr><h2>SQL x 10</h2><table border="1" cellpadding="3" cellspacing="1">';
        foreach ($sqlData as $sql) {
            $time_start = microtime(true);
            for ($i = 0; $i < 10; $i++) {
                $result = Yii::$app->db->createCommand($sql)->queryAll();
            }
            $time_end = microtime(true);
            echo '<tr><td>' . $sql . '</td><td>Time: ' . round($time_end - $time_start, 6) . '</td></tr>';
        }
        echo '</table>';
    }


    public function actionJson2()
    {
        $arr =
            [
                'type' => 'voip_incoming',
                'call_id' => '3522',
                'call' => [
                    'Called' => '+16692011257',
                    'ToState' => 'CA',
                    'CallerCountry' => 'DC',
                    'Direction' => 'inbound',
                    'CallerState' => '',
                    'ToZip' => '',
                    'CallSid' => 'CA4oubrgryx71mp1mig9sqykcqsizyxhln',
                    'To' => '+1?8888183963',
                    'CallerZip' => '',
                    'ToCountry' => 'US',
                    'ApiVersion' => '2010-04-01',
                    'CalledZip' => '',
                    'CalledCity' => '',
                    'CallStatus' => 'ringing',
                    'From' => '+15497061563',
                    'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c',
                    'CalledCountry' => 'US',
                    'CallerCity' => '',
                    'ApplicationSid' => 'APd65ba6826de6314e0780220d89fc6cde',
                    'Caller' => '+15497061563',
                    'FromCountry' => 'DC',
                    'ToCity' => '',
                    'FromCity' => '',
                    'CalledState' => 'CA',
                    'FromZip' => '20001',
                    'FromState' => '',
                ]
            ]
        ;

        echo json_encode($arr);
    }

    public function asJson($data)
    {
        return parent::asJson($data); // TODO: Change the autogenerated stub
    }


    public function actionHotelApi()
    {

        //$module = HotelModule::getInstance();

        $apiHotelService = Yii::$app->getModule('hotel')->apiService;
        // $service = $hotel->apiService;


        $rooms[] = ['rooms' => 1, 'adults' => 1];
        $rooms[] = ['rooms' => 2, 'adults' => 2, 'children' => 2, 'paxes' => [
            ['paxType' => 1, 'age' => 6],
            ['paxType' => 1, 'age' => 14],
        ]];

        $params['maxRate'] = 120;
        $params['maxHotels'] = 3;

        // MaxRatesPerRoom


        $response = $apiHotelService->search('2019-11-25 00:00:00', '2019-11-27 00:00:00', 'CAS', $rooms, $params);

        //VarDumper::dump($response, 10, true);
        echo json_encode($response);
    }

    public function actionGetCountWeekDays()
    {
        $week = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];

        $d1 = new DateTime('2019-12-01 01:00:00');
        $d2 = new DateTime('2019-12-31 15:00:00');

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($d1, $interval, $d2);

        foreach ($period as $date) {
            $week[$date->format('l')]++;
        }

        print_r($week);
    }

    public function actionGetCountHourDays()
    {
        $hours = [];
        for ($i = 0; $i <= 23; $i++) {
            $hours[$i] = 0;
        }

        $d1 = new DateTime('2019-12-01 01:00:00');
        $d2 = new DateTime('2019-12-31 15:00:00');

        $interval = DateInterval::createFromDateString('1 hour');
        $period   = new DatePeriod($d1, $interval, $d2);

        foreach ($period as $date) {
            $hours[$date->format('G')]++;
        }

        print_r($hours);
    }

    public function actionTestCurrencyHistoryLog()
    {
        $date = '2019-12-25';
        $currency = Currency::find()->all();
        foreach ($currency as $item) {
            $currencyHistory = (new CurrencyHistory())->findOrCreateByPrimaryKeys($item->cur_code, $date);

            $currencyHistory->ch_code = $item->cur_code;
            $currencyHistory->ch_base_rate = $item->cur_base_rate;
            $currencyHistory->ch_app_rate = $item->cur_app_rate;
            $currencyHistory->ch_app_percent = $item->cur_app_percent;
            $currencyHistory->ch_main_created_dt = $item->cur_created_dt;
            $currencyHistory->ch_main_updated_dt = $item->cur_updated_dt;
            $currencyHistory->ch_main_synch_dt = $item->cur_synch_dt;
            $currencyHistory->ch_created_date = $date;

            if (!$currencyHistory->save(false)) {
                Yii::error($currencyHistory->ch_code . ': ' . VarDumper::dumpAsString($currencyHistory->errors), 'Currency:synchronization:CurrencyHistory:save');
                echo 'Error';
                die;
            }
        }

        echo 'Successful';
    }

    public function actionEncrypt()
    {
        $text = 'Hello!';
        $encryptData = CreditCard::encrypt($text);
        $decryptData = CreditCard::decrypt($encryptData);

        VarDumper::dump($encryptData, 10, true);
        echo '<br><hr>';
        VarDumper::dump($decryptData, 10, true);
    }

    public function actionMask()
    {
        $number = '41112222333344445';
        //echo  substr_replace($number, str_repeat('*', strlen( $number ) - 4), 0, strlen( $number ) - 4);

        $creditCard[] = '5362267121053405'; // Mastercard
        $creditCard[] = '4556189015881361'; // Visa 16
        $creditCard[] = '4716904617062'; // Visa 13
        $creditCard[] = '372348371455844'; // American Express
        $creditCard[] = '6011757892594291'; // Discover
        $creditCard[] = '30329445722959'; // Diners Club
        $creditCard[] = '214927124363421'; // enRoute
        $creditCard[] = '180012855304868'; // JCB 15
        $creditCard[] = '3528066275370961'; // JCB 16
        $creditCard[] = '8699775919'; // Voyager

        for ($i = 0; $i < count($creditCard); $i++) {
            echo CreditCardHelper::formatCreditCard(CreditCardHelper::maskCreditCard($creditCard[$i])) . '<br>'; //FormatCreditCard(MaskCreditCard(($creditCard[$i])))."\n";
        }
    }


    public function actionFilter()
    {
        $array[] = ['price' => 1, 'gds' => 'A', 'data' => []];
        $array[] = ['price' => 2, 'gds' => 'B', 'data' => []];
        $array[] = ['price' => 3.5, 'gds' => 'C', 'data' => []];
        $array[] = ['price' => 1.3, 'gds' => 'A', 'data' => []];

        $result = AppHelper::filterByValue($array, 'gds', 'A');
        $result = AppHelper::filterByRange($array, 'price', null, 3);
        $result = AppHelper::filterByRange($array, 'price', 2, 3);
        $result = AppHelper::filterByArray($array, 'gds', ['A', 'B']);

        VarDumper::dump($result, 10, true);
    }

    public function actionTestEvents()
    {
        $result = [];

        $db = \Yii::$app->db;

        $transaction = $db->beginTransaction();

        try {
            $user = Employee::findOne(\Yii::$app->user->id);

            if ($user) {
                $user->save(false);
            }

            $notify = Notifications::findOne(1);

            if ($notify) {
                $notify->addEvent(
                    NotificationEvents::NOTIFY_SENT,
                    [NotificationEvents::class, 'send'],
                    $notify->n_title
                );

                //Event::on(Notifications::class, NotificationEvents::EVENT_NOTIFY, [NotificationEvents::class, 'send'], $notify->n_title);


                $notify->changeTitle('title ' . random_int(1, 100) . ' - ' . date('H:i:s'));

                $notify->addEvent(NotificationEvents::NOTIFY_UPDATE, [NotificationEvents::class, 'send2'], $notify->n_title);
                $notify->addEvent(NotificationEvents::NOTIFY_UPDATE, [NotificationEvents::class, 'send']);

                $notify->save();
                $notify->addEvent(NotificationEvents::NOTIFY_DELETE, [NotificationEvents::class, 'send'], $notify->attributes);
            }



            //$notify->on(NotificationEvents::EVENT_NOTIFY_DELETE, [NotificationEvents::class, 'send'], $notify->attributes);


            /*$rows = $db->createCommand('SELECT * FROM notifications WHERE n_id = 1')->queryAll();

            $rows = $db->createCommand('SELECT * FROM notifications WHERE n_id = 2')->queryAll();


            $db->createCommand("UPDATE notifications SET n_title='demo2' WHERE n_id = 1")->execute();*/

            //$db->createCommand("UPDATE notifications SET n_title2='demo2' WHERE n_id = 1")->execute();

            $transaction->commit();

            //$notify->trigger(NotificationEvents::EVENT_MESSAGE_SENT, new Event(['sender' => $user]));

            $result = $notify->triggerEvents();

            //$notify->trigger(NotificationEvents::EVENT_NOTIFY);
        } catch (\Exception $e) {
            $transaction->rollBack();
            VarDumper::dump($e->getMessage());
        } catch (\Throwable $e) {
            $transaction->rollBack();
            VarDumper::dump($e->getMessage());
        }

        //\Yii::$app->trigger('bar', new Event(['notify' => new Notifications()]));

        VarDumper::dump($result, 10, true);
    }

    public function actionCsv()
    {
        $fileName = Yii::getAlias('@console/runtime/1.csv');
        $content = file_get_contents($fileName);

        $rows = explode("\r\n", $content);
        $leads = [];
        if ($rows) {
            foreach ($rows as $rn => $row) {
                $rowData = explode(',', $row);
                if (!$rowData || $rn === 0) {
                    continue;
                }
                $lead = [
                    'first_name' => trim($rowData[0]),
                    'last_name' => trim($rowData[1]),
                    'email' => trim($rowData[2]),
                    'phone' => trim(str_replace([' ', '-', '(', ')'], '', $rowData[3])),
                    'rating' => (int) trim($rowData[4]),
                    'notes' => str_replace('"', '', trim($rowData[5])),
                    'source_code' => trim($rowData[6]),
                    'project_id' => (int) trim($rowData[7]),
                ];
                $leads[] = $lead;
            }
        }


        echo '<pre>';
        echo VarDumper::dump($leads, 10, true);
        echo '</pre>';
    }

    public function actionTestUserProfile()
    {
        $userProfile = new UserProfile();
        $userProfile->up_join_date = '2019-01-01';
        $expMonth = $userProfile->getExperienceMonth();
        var_dump($expMonth);
    }

    public function actionWebSocket()
    {
        $this->layout = 'main2';

        VarDumper::dump(Yii::$app->session->id);

        return $this->render('websocket');
    }

    public function actionTestCallHelper(): void
    {
        $callAccess = CallAccess::isUserCanDial(464, UserProfile::CALL_TYPE_WEB);

        $test1 = CallHelper::callNumber('+123456789', false);
        $test2 = CallHelper::callNumber('+123456789', $callAccess, 'call phone');
        $test3 = CallHelper::callNumber('+123456789', $callAccess, 'call phone', [
            'confirm' => 1,
            'call' => 1,
            'phone-from-id' => 34,
            'icon-class' => 'fa fa-phone valid'
        ]);
        $test4 = CallHelper::callNumber('+123456789', $callAccess, 'call phone', [
            'confirm' => 1,
            'call' => 1,
            'phone-from-id' => 34,
            'icon-class' => 'fa fa-phone valid',
        ], 'a');

        echo Html::encode($test1);
        echo '<br>';
        echo Html::encode($test2);
        echo '<br>';
        echo Html::encode($test3);
        echo '<br>';
        echo Html::encode($test4);
    }

    public function actionTestAddCreditCardBo()
    {
        $bookId = 'B2917FB';
        $saleId = 136503;

        $card = new CreditCardForm();
        $card->cc_holder_name = 'Alex Grub Test';
        $card->cc_number = '5555555555555555';
        $card->cc_expiration = '10 / 21';
        $card->cc_cvv = 111;

        $repository = Yii::createObject(CasesSaleRepository::class);

        $caseSale = $repository->getSaleByPrimaryKeys(135814, $saleId);

        $saleOriginalData = JsonHelper::decode($caseSale->css_sale_data);

        $service = Yii::createObject(CasesSaleService::class);
        echo '<pre>';
        print_r($service->sendAddedCreditCardToBO($saleOriginalData['projectApiKey'], $bookId, $saleId, $card));
    }

    public function actionPreview()
    {
        $gid = mb_substr('26958bc1be930b2213595af0ab40f586', 0, 32);
        $lead = Lead::find()->where(['gid' => $gid])->limit(1)->one();

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;

        $comForm = new CommunicationForm();
        $language = $comForm->c_language_id ?: 'en-US';
        $comForm->c_preview_email = 1;
        $tpl = 'cl_offer'/*EmailTemplateType::findOne($comForm->c_email_tpl_id)*/;
        $mailFrom = 'test@gmail.com';
        $mailTo = 'test2@gmail.com';

        $content_data['email_body_text'] = '2';

        $upp = null;
        if ($lead->project_id) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $lead->project_id, 'upp_user_id' => Yii::$app->user->id])->withEmailList()->one();
            if ($upp) {
                $mailFrom = $upp->getEmail();
            }
        }

        $project = $lead->project;
        $projectContactInfo = [];

        if ($project && $project->contact_info) {
            $projectContactInfo = @json_decode($project->contact_info, true);
        }

        $content_data = $lead->getEmailData2(['916469'], $projectContactInfo);

        //VarDumper::dump($content_data, 10 , true); exit;


        $mailPreview = $communication->mailCapture($lead->project_id, ($tpl ? $tpl/*$tpl->etp_key*/ : ''), $mailFrom, $mailTo/*$comForm->c_email_to*/, $content_data, $language);
        VarDumper::dump($mailPreview, 10, true);
        exit;
    }

    public function actionVue()
    {
        return $this->render('vue');
    }

    public function actionReact()
    {
        return $this->render('react');
    }

    public function actionCallWidget()
    {
        return $this->render('call-widget');
    }

    public function actionRchat()
    {
        $chat = Yii::$app->rchat;


//        "email": "test@gmail.com",
        //  "name": "John Balon",
        //  "password": "test",
        //  "username": "test",
        //  "active": true,
        //  "roles": ["user", "livechat-agent"],
        //  "joinDefaultChannels": false

        //VarDumper::dump($chat->getSystemAuthData(), 10, true);
        //VarDumper::dump($chat->getAllDepartments(), 10, true);

        VarDumper::dump($chat->createUser('alex.connor4', 'alex.connor4', 'alex.connor4', 'alex.connor4@techork.com'), 10, true);
        //VarDumper::dump($chat->createUser('alex.connor5', 'alex.connor5', 'alex.connor5', 'alex.connor5@techork.com'), 10, true);

        VarDumper::dump($chat->deleteUser('alex.connor4'), 10, true);
        //VarDumper::dump($chat->deleteUser('alex.connor5'), 10, true);
        //VarDumper::dump($chat->systemLogin(), 10, true);
    }

    public function actionTestRcAssignUserToChannel()
    {

//      $response = Yii::$app->rchat->setActiveStatus('Wvuk6YpiZJdgWeiaL', '9HFxzz2zfZsN7eMhv', 'juHeZVXwDSR4wLuap2fC5dqS4tR39M4CcAHwVeeph2O');
//      $response = Yii::$app->rchat->setStatus(RocketChat::STATUS_ONLINE, 'Wvuk6YpiZJdgWeiaL', '9oUd_DynNc1Rc-in9ZUPrSBh62fNhwl-bR4oj-23tYy');
//      var_dump($response);die;

        try {
            $clientChatService = Yii::createObject(ClientChatService::class);
            $clientChatService->assignAgentToRcChannel('0a0aed99-a191-436c-8c79-cb5a55770bbe', 'Wvuk6YpiZJdgWeiaL');
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            die;
        }

        echo 'success';
    }

    public function actionGaSendQuote($id = 733986, int $debug = 1) // test/ga-send-quote?id=733986&debug=1
    {
        try {
            if (is_int($id)) {
                $params = ['id' => $id];
            } else {
                $params = ['uid' => $id];
            }
            $quote = Quote::findOne($params);
            $gaQbj = new GaQuote($quote);

            $gaRequestService = \Yii::$app->gaRequestService;
            \Yii::configure($gaRequestService, ['debugMod' => (bool) $debug]);
            $response = $gaRequestService->sendRequest($gaQbj->getPostData());

            VarDumper::dump([
                'post Data' => $gaQbj->getPostData(),
                'response' => $response,
            ], 10, true);
        } catch (\Throwable $throwable) {
            VarDumper::dump(AppHelper::throwableFormatter($throwable), 10, true);
        }
        exit();
    }

    public function actionGaSendLead(int $id = 367010, int $debug = 1) // test/ga-send-lead?id=367010&debug=1
    {
        try {
            $lead = Lead::findOne($id);
            $gaQbj = new GaLead($lead);

            $gaRequestService = \Yii::$app->gaRequestService;
            \Yii::configure($gaRequestService, ['debugMod' => (bool) $debug]);
            $response = $gaRequestService->sendRequest($gaQbj->getPostData());

            VarDumper::dump([
                'post Data' => $gaQbj->getPostData(),
                'response' => $response,
            ], 10, true);
        } catch (\Throwable $throwable) {
            VarDumper::dump(AppHelper::throwableFormatter($throwable), 10, true);
        }
        exit();
    }

    public function actionLocaleParams(string $gid, string $locale)
    {
        $content_data = [];
        if ($model = Cases::findOne(['cs_gid' => $gid])) {
            $casesCommunicationService = \Yii::createObject(CasesCommunicationService::class);
            $content_data = $casesCommunicationService->getEmailData($model, Auth::user(), $locale);
        }
        VarDumper::dump($content_data, 10, true);
        exit();
    }

    public function actionProjectLocaleParams(int $project_id, int $client_id, string $locale)
    {
        $casesCommunicationService = \Yii::createObject(CasesCommunicationService::class);

        if (!$client = Client::findOne($client_id)) {
            throw new NotFoundHttpException('Client not found.');
        }

        $result = $casesCommunicationService::getLocaleParams($project_id, $client, $locale);

        VarDumper::dump([
            'ParamProjectId' => $project_id,
            'ParamLocale' => $locale,
            'ParamClientId' => $client->id,
            'ClientInfoLocale' => $client->cl_locale,
            'ClientInfoMarketingCountry' => $client->cl_marketing_country,
            str_repeat('=', 20) => str_repeat('=', 20),
            'ResultProjectLocaleParam' => $result,
        ], 10, true);
        exit();
    }

    public function actionFlushMetrics()
    {
        try {
            $adapter = Yii::createObject(\Prometheus\Storage\Redis::class);
            $adapter::setDefaultOptions(Yii::$app->prometheus->redisOptions);
            $adapter->wipeStorage();
        } catch (\Throwable $throwable) {
            VarDumper::dump($throwable->getMessage(), 10, true);
        }
        exit('Done');
    }

    public function actionFlightQuotePdf(int $id, int $json = 1)
    {
        if (!$flightQuote = FlightQuote::findOne(['fq_id' => $id])) {
            throw new NotFoundHttpException('FlightQuote not found');
        }
        try {
            $generatedData = FlightQuoteTicketIssuedService::generateTicketIssued($flightQuote, true);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable, true), 'Test:actionFlightQuotePdf');
            VarDumper::dump($throwable->getMessage(), 20, true);
            exit();
        }

        if ($json === 1) {
            return $this->asJson($generatedData);
        }
        VarDumper::dump($generatedData, 20, true);
        exit();
    }

    public function actionReceiptPdf(int $order_id, int $data = 0, int $json = 1)
    {
        if (!$order = Order::findOne(['or_id' => $order_id])) {
            throw new NotFoundHttpException('Order not found');
        }
        $orderPdfService = new OrderPdfService($order);
        if ($data === 1) {
            if ($json === 1) {
                return $this->asJson($orderPdfService->getCommunicationData());
            }
            VarDumper::dump($orderPdfService->getCommunicationData(), 20, true);
            exit();
        }
        try {
            $orderPdfService->processingFile();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'Test:actionHotelQuoteFile');
            VarDumper::dump($throwable->getMessage(), 10, true);
            exit();
        }
        return $orderPdfService->generateForBrowserOutput();
    }

    public function actionOrderData(int $order_id, int $json = 0)
    {
        if (!$order = Order::findOne(['or_id' => $order_id])) {
            throw new NotFoundHttpException('Order not found');
        }

        $data['order'] = $order->serialize();
        $data['project_key'] = $order->orLead->project->project_key;

        if ($json) {
            return $this->asJson($data);
        }
        VarDumper::dump($data, 20, true);
        exit();
    }

    public function actionHotelQuotePdf(int $id, int $data = 0, int $json = 1)
    {
        if (!$quote = HotelQuote::findOne(['hq_id' => $id])) {
            throw new NotFoundException('HotelQuote not found. Id (' . $id . ')');
        }
        HotelQuotePdfService::guard($quote);
        $pdfService = new HotelQuotePdfService($quote);
        if ($data === 1) {
            if ($json === 1) {
                return $this->asJson($pdfService->getCommunicationData());
            }
            VarDumper::dump($pdfService->getCommunicationData(), 20, true);
            exit();
        }
        try {
            $pdfService->processingFile();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'Test:actionHotelQuoteFile');
            VarDumper::dump($throwable->getMessage(), 10, true);
            exit();
        }
        return $pdfService->generateForBrowserOutput();
    }

    public function actionAPdf(int $id, int $data = 0, int $json = 1)
    {
        if (!$quote = AttractionQuote::findOne(['atnq_id' => $id])) {
            throw new NotFoundException('AttractionQuote not found. Id (' . $id . ')');
        }
        $pdfService = new AttractionQuotePdfService($quote);
        if ($data === 1) {
            if ($json === 1) {
                return $this->asJson($pdfService->getCommunicationData());
            }
            VarDumper::dump($pdfService->getCommunicationData(), 20, true);
            exit();
        }
        try {
            $pdfService->processingFile();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'actionAPdf');
            VarDumper::dump($throwable->getMessage(), 20, true);
            exit();
        }
        return $pdfService->generateForBrowserOutput();
    }

    public function actionCarPdf(int $id, int $data = 0, int $json = 1)
    {
        if (!$quote = RentCarQuote::findOne(['rcq_id' => $id])) {
            throw new NotFoundException('RentCarQuote not found. Id (' . $id . ')');
        }
        $pdfRC = new RentCarQuotePdfService($quote);
        if ($data === 1) {
            if ($json === 1) {
                return $this->asJson($pdfRC->getCommunicationData());
            }
            VarDumper::dump($pdfRC->getCommunicationData(), 20, true);
            exit();
        }
        try {
            $pdfRC->processingFile();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'TestController:actionCarPdf');
            VarDumper::dump($throwable->getMessage(), 20, true);
            exit();
        }
        return $pdfRC->generateForBrowserOutput();
    }

    public function actionPqPdf(int $id, int $data = 0, int $json = 1)
    {
        if (!$productQuote = ProductQuote::findOne(['pq_id' => $id])) {
            throw new NotFoundException('ProductQuote not found. Id (' . $id . ')');
        }
        if (!$quote = $productQuote->getChildQuote()) {
            throw new NotFoundException('ChildQuote not found. Id (' . $id . ')');
        }

        if ($productQuote->isFlight()) {
            /** @var FlightQuote $quote */
            $pdfService = new FlightQuotePdfService($quote);
        } elseif ($productQuote->isHotel()) {
            /** @var HotelQuote $quote */
            HotelQuotePdfService::guard($quote);
            $pdfService = new HotelQuotePdfService($quote);
        } elseif ($productQuote->isRentCar()) {
            /** @var RentCarQuote $quote */
            $pdfService = new RentCarQuotePdfService($quote);
        } elseif ($productQuote->isAttraction()) {
            /** @var AttractionQuote $quote */
            $pdfService = new AttractionQuotePdfService($quote);
        } else {
            throw new NotFoundException('Only Flight/Hotel/RC ProductQuote');
        }

        if ($data === 1) {
            if ($json === 1) {
                return $this->asJson($pdfService->getCommunicationData());
            }
            VarDumper::dump($pdfService->getCommunicationData(), 20, true);
            exit();
        }

        try {
            $pdfService->processingFile();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'TestController:actionPqPdf');
            VarDumper::dump($throwable->getMessage(), 20, true);
            exit();
        }
        return $pdfService->generateForBrowserOutput();
    }

    public function actionZ()
    {
        $job = new UserTaskCompletionJob(
            TargetObject::TARGET_OBJ_LEAD,
            513424,
            TaskObject::OBJ_EMAIL,
            29158,
            Auth::id()
        );
        $x = Yii::$app->queue_job->push($job);

        echo 'Feature Flag Test<br><br>';

        /** @fflag FFlag::FF_KEY_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LPP_ENABLE)) {
            VarDumper::dump(Yii::$app->featureFlag->getValue(FFlag::FF_KEY_LPP_ENABLE), 10, true);
        } else {
            echo 'NO1';
        }

        exit('<br />Stop');

        return $this->render('z');
    }

    /**
     * @throws \yii\httpclient\Exception
     */
    public function actionAirportExport()
    {
        $airline = Yii::$app->travelServices->airportExport(0, 20000);
        VarDumper::dump($airline, 10, true);
    }

    public function actionUserMonitor()
    {
        return $this->render('user-monitor');
    }

    public function actionTestSetByUserId()
    {
//        $repository = Yii::createObject(ClientChatUserAccessService::class);
//
//        $repository->setUserAccessToAllChats(464);
    }

    public function actionSetAccessToAllChatsByChannelIds()
    {
        try {
            $userAccessService = Yii::createObject(ClientChatUserAccessService::class);

            $userAccessService->disableUserAccessToAllChats(464);
            $userAccessService->setUserAccessToAllChatsByChannelIds([5], 464);
        } catch (\Throwable $e) {
            echo AppHelper::throwableFormatter($e);
        }
    }

    public function actionErrors()
    {
        $message = 'Test message ' . date('Y-m-d H:i:s');

        Yii::error('Error: ' . $message, 'error\TestController:actionErrors');
        Yii::warning('Warning: ' . $message, 'warning\TestController:actionErrors');
        Yii::info('Info: ' . $message, 'info\TestController:actionErrors');


        try {
//            $a = 3 / 0;
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable, true), 'error\TestController:actionErrors:Throwable');
        }

        echo 'Test Error, Warning, Info - ' . date('Y-m-d H:i:s');
    }

    public function actionLock(): string
    {
        $redis = Yii::$app->redis;
        $key = 'test-lock';

        //VarDumper::dump($redis->get($key));

        if ($redis->get($key)) {
            return 'lock';
        } else {
            $redis->setnx($key, 1);
            $redis->expire($key, 10);
            //sleep(3);
        }
        return 'ok';
    }

    public function actionPrometheus(): string
    {
        $registry = Yii::$app->prometheus->registry;
        $counter = $registry->registerCounter('frontend', 'some_counter234', 'it increases', ['a', 's', 'd']);
        $counter->incBy(2, ['blue', '34', 3]);
        $counter->incBy(1, ['red', '35', 4]);

        $counter = $registry->getOrRegisterCounter('backend', 'some_counter', 'it increases', ['type']);
        $counter->incBy(3, ['blue']);

        $gauge = $registry->getOrRegisterGauge('console', 'some_gauge', 'it sets', ['type']);
        $gauge->set(2.5, ['blue']);

        $histogram = $registry->getOrRegisterHistogram('webapi', 'some_histogram', 'it observes', ['type'], [0.1, 1, 2, 3.5, 4, 5, 6, 7, 8, 9]);
        $histogram->observe(3.5, ['blue']);

        //return  '123';
        return Yii::$app->prometheus->getMetric();
    }


    public function actionMetrics(): string
    {
        /** @var Metrics $metrics */
        $metrics = \Yii::$container->get(Metrics::class);

        $metrics->serviceCounter('test1', []);
        $metrics->serviceCounter('test1', []);
        $metrics->serviceCounter('test1', []);
        $metrics->serviceCounter('test1', []);
        $metrics->serviceCounter('test1', []);

        $metrics->jobCounter('job1', ['name' => 'Alexandr']);
        $metrics->jobCounter('job1', ['name' => 'Dendy']);
        $metrics->jobCounter('job1', ['name' => 'Alexandr']);
        $metrics->jobCounter('job1', ['name' => 'Dendy']);


        $metrics->jobHistogram('histogram2', 0.3, ['name' => 'Dendy']);
        $metrics->jobHistogram('histogram2', 0.5, ['name' => 'Alexandr']);


        $metrics->jobGauge('gauge1', 3.4, ['type' => 'Dendy2']);
        $metrics->jobGauge('gauge1', 2.7, ['type' => 'Alexandr3']);
        $metrics->jobGauge('gauge1', 3.2, ['type' => 'Dendy2']);


        $metrics->jobCounter('count');
        $metrics->jobHistogram('hst', random_int(0, 7));
        $metrics->jobHistogram('hst2', random_int(0, 7));
        $metrics->jobGauge('test2', random_int(-100, 100));
        $metrics->jobGauge('test1', random_int(-100, 100));



        /*$timeStart = microtime(true);


        sleep(random_int(0, 2));
        $seconds = round(microtime(true) - $timeStart, 1);
        echo $seconds;
        exit;*/

        return Yii::$app->prometheus->getMetric();
    }


    public function actionLocale()
    {
        Yii::$app->language = 'ru-RU';
        Yii::$app->formatter->locale = 'ru-CA';

        echo '<hr>';
        echo 'lang: ' . Yii::$app->language . '<br>';
        echo 'locale: ' . Yii::$app->formatter->locale . '<br>';
        echo 'Decimal: ' . Yii::$app->formatter->asDecimal(1234.5678) . '<br>';
        echo 'Currency: ' . Yii::$app->formatter->asCurrency(1234.5678) . '<br>';
        echo 'Date: ' . Yii::$app->formatter->asDate('2014-01-01') . '<br>';

        echo 'DateTime: ' . Yii::$app->formatter->asDatetime(time()) . '<br><hr>';


        //Yii::$app->formatter->locale = 'ru-RU';
        Yii::$app->language = 'en-US';



        echo 'lang: ' . Yii::$app->language . '<br>';
        echo 'locale: ' . Yii::$app->formatter->locale . '<br>';
        echo 'Decimal: ' . Yii::$app->formatter->asDecimal(1234.5678) . '<br>';
        echo 'Currency: ' . Yii::$app->formatter->asCurrency(1234.5678) . '<br>';
        echo 'Date: ' . Yii::$app->formatter->asDate('2014-01-01') . '<br>';
        echo 'DateTime: ' . Yii::$app->formatter->asDatetime(time()) . '<br>';

        Yii::$app->formatter->locale = 'SK';
        Yii::$app->language = 'ru-RU';


        echo '<hr>';
        echo 'lang: ' . Yii::$app->language . '<br>';
        echo 'locale: ' . Yii::$app->formatter->locale . '<br>';
        echo 'Decimal: ' . Yii::$app->formatter->asDecimal(1234.5678) . '<br>';
        echo 'Currency: ' . Yii::$app->formatter->asCurrency(1234.5678) . '<br>';
        echo 'Date: ' . Yii::$app->formatter->asDate('2014-01-01') . '<br>';
        echo 'DateTime: ' . Yii::$app->formatter->asDatetime(time()) . '<br>';
    }

    /**
     * @throws \yii\httpclient\Exception
     */
    public function actionCallAntiSpam()
    {
        $dto = new CallAntiSpamDto();

        echo '<h1>CallAntiSpam</h1>Data:<br>';
        VarDumper::dump($dto->getData(), 10, true);

        echo '<br><br>Response:<br>';

        $response = Yii::$app->callAntiSpam->checkData($dto);
        if (!empty($responce['error'])) {
            try {
                $errorData = json_decode($response['error'], true);
                VarDumper::dump($errorData, 10, true);
            } catch (\Throwable $throwable) {
                VarDumper::dump($response['error'], 10, true);
            }
        } else {
            VarDumper::dump($response['data'], 10, true);
        }
    }

    public function actionAbac()
    {
        if (Yii::$app->abac->can(null, OrderAbacObject::ACT_READ, OrderAbacObject::ACTION_READ)) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }

    public function actionErrorTest()
    {
        $message = [
            'message' => 'Test message 1',
            'trace' => ['tr1' => 'ttttttttttt1'],
            'a1' => '1111',
            'b2' => '222',
            'с3' => [
                'message' => 'Test message 21',
                'trace' => ['tr1' => 'ttttttttttt1'],
                'b' => '1111',
                'b2' => '222',
                'b3' => [
                    'message' => 'Test message 31',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => '222',
                ],
            ],
        ];


        $message = [
            'message' => 'Test message 1',
            'trace' => ['tr1' => 'ttttttttttt1'],
            'a1' => '1111',
            'b2' => '222',
            'с3' => [
                'message1' => 'Test message 21',
                'trace' => ['tr1' => 'ttttttttttt1'],
                'b' => [
                    'message3' => 'Test message 1',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => [
                        'message4' => 'Test message 21',
                        'trace' => ['tr1' => 'ttttttttttt1'],
                        'b' => '1111',
                        'b2' => '222',
                        'b3' => [
                            'message5' => 'Test message 31',
                            'trace' => ['tr1' => 'ttttttttttt1'],
                            'a1' => '1111',
                            'b2' => '222',
                            'с3' => '222',
                        ],
                    ],
                ],
                'b2' => '222',
                'b3' => [
                    'message2' => 'Test message 31',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => [
                        'message3' => 'Test message 1',
                        'trace' => ['tr1' => 'ttttttttttt1'],
                        'a1' => '1111',
                        'b2' => '222',
                        'с3' => [
                            'message4' => 'Test message 21',
                            'trace' => ['tr1' => 'ttttttttttt1'],
                            'b' => '1111',
                            'b2' => '222',
                            'b3' => [
                                'message5' => 'Test message 31',
                                'trace' => ['tr1' => 'ttttttttttt1'],
                                'a1' => '1111',
                                'b2' => '222',
                                'с3' => '222',
                            ],
                        ],
                    ],
                ],
            ],
        ];

//        Yii::error($message, 'test/error');
//        Yii::warning($message, 'test/warning');
//        Yii::info($message, 'info\test/info');
//        Yii::info($message, 'log\test/log');
//
//        $message = 'Test message 2';
//
//        Yii::error($message, 'test/error');
//        Yii::warning($message, 'test/warning');
//        Yii::info($message, 'info\test/info');
//        Yii::info($message, 'log\test/log');

//        $a = new StartDTO();
//        $a->ip = 'asdasdasd';
//
//        VarDumper::dump($a);
//        Yii::error($a, 'error:Throwable2');
//        exit;

        try {
//            $a = 3 / 0;
        } catch (\Throwable $throwable) {
            //VarDumper::dump(get_object_vars($throwable), 10, true);
            //VarDumper::dump(AppHelper::throwableLog($throwable, true), 10, true);
            Yii::error(AppHelper::throwableLog($throwable, true), 'error:Throwable');
            //Yii::error($throwable, 'error:Throwable');
        }

        return date('Y-m-d h:i:s');
    }

    public function actionArrayTrim()
    {
        $message = [
            'message' => 'Test message 1',
            'trace' => ['tr1' => 'ttttttttttt1'],
            'a1' => '1111',
            'b2' => '222',
            'с3' => [
                'message1' => 'Test message 21',
                'trace' => ['tr1' => 'ttttttttttt1'],
                'b' => [
                    'message3' => 'Test message 1',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => [
                        'message4' => 'Test message 21',
                        'trace' => ['tr1' => 'ttttttttttt1'],
                        'b' => '1111',
                        'b2' => '222',
                        'b3' => [
                            'message5' => 'Test message 31',
                            'trace' => ['tr1' => 'ttttttttttt1'],
                            'a1' => '1111',
                            'b2' => '222',
                            'с3' => '222',
                        ],
                    ],
                ],
                'b2' => '222',
                'b3' => [
                    'message2' => 'Test message 31',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => [
                        'message3' => 'Test message 1',
                        'trace' => ['tr1' => 'ttttttttttt1'],
                        'a1' => '1111',
                        'b2' => '222',
                        'с3' => [
                            'message4' => 'Test message 21',
                            'trace' => ['tr1' => 'ttttttttttt1'],
                            'b' => '1111',
                            'b2' => '222',
                            'b3' => [
                                'message5' => 'Test message 31',
                                'trace' => ['tr1' => 'ttttttttttt1'],
                                'a1' => '1111',
                                'b2' => '222',
                                'с3' => '222',
                            ],
                        ],
                    ],
                ],
            ],
        ];


        VarDumper::dump($message, 10, true);


        echo '<hr>';
        $message = AppHelper::shotArrayData($message);

        VarDumper::dump($message, 10, true);


        exit;
    }

    public function actionAt()
    {
        $message = [
            'message' => 'Test message 1',
            'trace' => ['tr1' => 'ttttttttttt1'],
            'a1' => '1111',
            'b2' => '222',
            'с3' => [
                'message' => 'Test message 21',
                'trace' => ['tr1' => 'ttttttttttt1'],
                'b' => '1111',
                'b2' => '222',
                'b3' => [
                    'message' => 'Test message 31',
                    'trace' => ['tr1' => 'ttttttttttt1'],
                    'a1' => '1111',
                    'b2' => '222',
                    'с3' => '222',
                ],
            ],
        ];

        Yii::info($message, 'analytics\analytics-test');
        Yii::info($message, 'AS\AS-test');
        Yii::info($message, 'elk\test-elk');

        return date('Y-m-d H:i:s');
    }

    public function actionFf()
    {
        echo 'Feature Flag "' . FFlag::FF_KEY_DEBUG . '"<br><br>';

//        Yii::$app->featureFlag->invalidateCache();
//
//        VarDumper::dump(Yii::$app->featureFlag->getFFListWOCache() /*getFFList()*/, 10, true);
//        exit;


        $debugDTO = new DebugFeatureFlagDTO();
        $debugDTO->department_id = 1;
        $debugDTO->project_key = 'ovago';
        $debugDTO->app_type = Yii::$app->params['serviceType'] ?? null;

        $userDTO = new UserFeatureFlagDTO(Auth::user());
        $dateTimeDTO = new DateTimeFeatureFlagDTO();


        $featureFlagDTO[DebugFeatureFlag::OBJ] = $debugDTO;
        $featureFlagDTO[UserFeatureFlag::OBJ] = $userDTO;
        $featureFlagDTO[DateTimeFeatureFlag::OBJ] = $dateTimeDTO;

//        VarDumper::dump($featureFlagDTO, 10, true);

        /** @fflag FFlag::FF_KEY_DEBUG, Username field1 */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG, $featureFlagDTO)) {
            echo 'Enable<br>';
            echo 'Value: ';
            // VarDumper::dump(Yii::$app->featureFlag->getValueItem(FFlag::FF_KEY_DEBUG, $featureFlagDTO), 10, true);

            for ($i = 0; $i <= 100; $i++) {
                //VarDumper::dump(
                    Yii::$app->featureFlag->getValue(FFlag::FF_KEY_DEBUG);
                    //, 10, true);
            }
        } else {
            echo 'Disable';
        }



        VarDumper::dump(Yii::$app->featureFlag->getExperimentLogList(), 10, true);
        VarDumper::dump(Yii::$app->featureFlag->getExperimentLogByKey(FFlag::FF_KEY_DEBUG), 10, true);

//
//        echo '<br><br>';
//
//        if (Yii::$app->featureFlag->isDue(\kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED_CONDITION, '59 * * * * *')) {
//            echo 'YES3';
//        } else {
//            echo 'NO3';
//        }

        echo '<br><br>';
        return date('Y-m-d H:i:s');
    }

    public function actionRandomProbability()
    {
        $games = [
            ['name' => 'Game 1', 'ver' => 2], //  2/15
            ['name' => 'Game 2', 'ver' => 0], // probability 0/15
            ['name' => 'Game 3', 'ver' => 1], // probability 1/15
            ['name' => 'Game 4', 'ver' => 4], // probability 4/15
            ['name' => 'Game 5', 'ver' => 8], // probability 8/15
        ];


//        $games = [
//            ['name' => 'Game 1', 'ver' => 50], // probability 2/15
//            ['name' => 'Game 2', 'ver' => 50], // probability 0/15
//        ];

        $data = [];
        $totalCount = 0;

        for ($a = 1; $a < 1000; $a++) {
            $i = AppHelper::getRandomProbabilityIndex($games, 'ver');
            //echo $games[$i]['name'];
            if (empty($data[$i])) {
                $data[$i]['cnt'] = 0;
            }
            $data[$i]['cnt'] ++;
            $totalCount++;
        }
        if (!$totalCount) {
            $totalCount = 1;
        }

        foreach ($data as $i => $item) {
            $percent = round($item['cnt'] * 100 / $totalCount, 2);
            //$data[$i]['percent'] = $percent;
            $games[$i]['percent'] = $percent;
        }

        VarDumper::dump($games, 10, true);
    }

    public function actionEvent()
    {
        $lead = Lead::find()->limit(1)->one();

//        NativeEventDispatcher::recordEvent(
//            LeadEvents::class,
//            LeadEvents::EVENT_CLOSE,
//            [EventHandler::class, 'handler'],
//            $lead->attributes
//        );
//        NativeEventDispatcher::trigger(LeadEvents::class, LeadEvents::EVENT_CLOSE);

//        Yii::$app->event->getEventListContent();

//
//        VarDumper::dump(Yii::$app->event->getEventListContent(), 10, true);
//
//        exit;

//        VarDumper::dump(LeadEvents::getHandlerList(), 10, true);
//        exit;



//        NativeEventDispatcher::recordEvent(
//            LeadEvents::class,
//            LeadEvents::EVENT_CLOSE,
//            [EventApp::class, EventApp::HANDLER],
//            [1, 2] //$lead->attributes
//        );
//
//        //NativeEventDispatcher::trigger(LeadEvents::class, LeadEvents::EVENT_CLOSE);
//
//
//        Event::on(
//            LeadEvents::class,
//            LeadEvents::EVENT_CLOSE,
//            [EventApp::class, EventApp::HANDLER],
//            ['a' => 123, 'b' => 'sdfsdf'],
//            false
//        );

        Event::on(
            LeadEvents::class,
            LeadEvents::EVENT_CLOSE,
            [EventApp::class, EventApp::HANDLER],
            ['a' => 123, 'b' => 'abc']
        );


        Event::trigger(
            LeadEvents::class,
            LeadEvents::EVENT_CLOSE
        );


        //VarDumper::dump($lead->attributes, 10, true);
    }

    public function actionHourFormat()
    {
        $hour = 135.3;
        echo Yii::$app->formatter->asDuration($hour * 60 * 60);

        $minutes = 1511;
        echo \Yii::$app->formatter->asHoursDuration($minutes);




      //  echo intdiv($minutes, 60).':'. ($minutes % 60);

        exit;
    }

    public function actionTestAbac()
    {
        $time_start = microtime(true);
        for ($i = 0; $i <= 1000; $i++) {
            Yii::$app->abac->can(null, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_READ);
            //Yii::$app->abac->can(null, SaleListAbacObject::UI_SALE_ID, SaleListAbacObject::ACTION_READ);
        }
        $time_end = microtime(true);
        echo 'Time: ' . round($time_end - $time_start, 6) . '';
    }


    public function actionClickhouse()
    {

        $db = \Yii::$app->clickhouse;
        $query = new \kak\clickhouse\Query();
        // first argument scalar var or Query object
        //$query->withQuery($db->quoteValue('2021-10-05'), 'date1');
        $query->select('1');
        //$query->from('stat');
        //$query->where('event_stat < date1');
        $query->all();

        $command = $query->createCommand();
        $data = $query->all();
        $result  = $command->queryAll();
        $total   = $command->getTotals();

        VarDumper::dump(['result' => $result, 'total' => $total, 'data' => $data]);
    }

    public function actionUserActivityUnite()
    {

        $data = UserActivityService::getUniteEventsByUserId(
            Auth::id(),
            date('Y-m-08 00:00:00'),
            date('Y-m-d 20:00:00'),
            UserEvents::EVENT_ACTIVE
        );
        VarDumper::dump($data, 10, true);
        /*$db = \Yii::$app->clickhouse;
        $query = new Query();
        // first argument scalar var or Query object
        //$query->withQuery($db->quoteValue('2021-10-05'), 'date1');
        $query->select('1');
        //$query->from('stat');
        //$query->where('event_stat < date1');
        $query->all();

        $command = $query->createCommand();
        $data = $query->all();
        $result  = $command->queryAll();
        $total   = $command->getTotals();

        VarDumper::dump(['result' => $result, 'total' => $total, 'data' => $data]);*/
    }


    public function actionTestSendQuote()
    {
        $leads = Lead::find()->andWhere(['status' => Lead::STATUS_PROCESSING])->limit(1000)->orderBy('id DESC')->all();
        $time_start = microtime(true);
        foreach ($leads as $lead) {
            $query = new Query();
            $query->select(['SUM(CASE WHEN status IN (2, 4, 5) THEN 1 ELSE 0 END) AS send_q',
                'SUM(CASE WHEN status NOT IN (2, 4, 5) THEN 1 ELSE 0 END) AS not_send_q'])
                ->from(Quote::tableName() . ' q')
                ->where(['lead_id' => $lead->id]);

            $query->createCommand()->queryOne();
        }
        $time_end = microtime(true);
        echo 'Time: ' . round($time_end - $time_start, 6) . '';
    }

    public function actionTestSendQuoteNew()
    {
        $leads = Lead::find()->andWhere(['status' => Lead::STATUS_PROCESSING])->limit(1000)->orderBy('id DESC')->all();
        $time_start = microtime(true);
        foreach ($leads as $lead) {
            $query = new Query();
            $query
                ->select(
                    [
                        'total' => 'COUNT(*)',
                        'send_q' => "SUM((SELECT SUM(CASE WHEN (status = :status) THEN 1 ELSE 0 END)
                         FROM `quote_status_log` WHERE `q`.id = `quote_status_log`.quote_id))"
                    ]
                )
                ->addParams([':status' => Quote::STATUS_SENT])
                ->from(Quote::tableName() . ' q')
                ->where(['lead_id' => $lead->id]);
            $query->createCommand()->queryOne();
        }
        $time_end = microtime(true);
        echo 'Time: ' . round($time_end - $time_start, 6) . '';
    }

    public function actionTestSendQuoteMiddle()
    {
        $leads = Lead::find()->andWhere(['status' => Lead::STATUS_PROCESSING])->limit(1000)->orderBy('id DESC')->all();
        $time_start = microtime(true);
        foreach ($leads as $lead) {
            $query = new Query();
            $query->select([
                'SUM(CASE WHEN statusCount > 0 THEN 1 ELSE 0 END) AS send_q',
                'SUM(CASE WHEN statusCount IS NULL OR statusCount = 0 THEN 1 ELSE 0 END) AS not_send_q'
            ])
                ->leftJoin(
                    [
                        'quote_status_log' => QuoteStatusLog::find()
                            ->select(['quote_id', 'COUNT(quote_id) as statusCount'])
                            ->where(['status' => [Quote::STATUS_SENT, Quote::STATUS_OPENED, Quote::STATUS_APPLIED]])
                            ->groupBy(['quote_id'])
                    ],
                    'quote_status_log.quote_id = q.id'
                )
                ->from(Quote::tableName() . ' q')
                ->where(['lead_id' => $lead->id]);
            $query->createCommand()->queryOne();
        }
        $time_end = microtime(true);
        echo 'Time: ' . round($time_end - $time_start, 6) . '';
    }
}
