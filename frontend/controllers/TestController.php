<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\components\CountEvent;
use common\components\jobs\TelegramSendMessageJob;
use common\models\Call;
use common\models\Employee;
use common\models\Notifications;
use common\models\Project;
use common\models\UserCallStatus;
use common\models\UserConnection;
use common\models\UserGroupAssign;
use common\models\UserProfile;
use common\models\UserProjectParams;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use common\components\ReceiveEmailsJob;
use yii\queue\Queue;
use common\components\CheckPhoneNumberJob;
use yii\rbac\ManagerInterface;


/**
 * Test controller
 */
class TestController extends FController
{
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
                        'roles' => ['admin'],
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

    public function actionT()
    {


        die;
        $roles = [];

        $array = [
            [
                'controller' => 'AgentReportController',
                'rules' => [
                    [
                        'actions' => ['index','calls','sms','email','cloned','created','sold','from-to-leads'],
                        'allow' => true,
                        'roles' => ['supervision','admin'],
                    ],
                ],
            ],
            [
                'controller' => 'ApiLogController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'create', 'delete', 'delete-all'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ApiUserController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'CallController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete', 'list', 'user-map', 'all-read'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin', 'qa'],
                    ],

                    [
                        'actions' => ['delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list', 'auto-redial'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'CleanController',
                'rules' => [
                    [
                        'actions' => ['index', 'cache', 'assets', 'runtime'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],

                ],
            ],
            [
                'controller' => 'ClientController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'test', 'ajax-get-info'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index', 'view', 'ajax-get-info'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ClientPhoneController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'EmailController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],

                    [
                        'actions' => ['delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['inbox', 'view', 'soft-delete'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'EmailTemplateTypeController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'synchronization'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'EmployeeController',
                'rules' => [
                    [
                        'actions' => ['switch'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['list', 'update', 'create', 'acl-rule'],
                        'allow' => true,
                        'roles' => ['supervision', 'userManager'],
                    ],
                    [
                        'actions' => ['seller-contact-info'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'KpiController',
                'rules' => [
                    [
                        'actions' => ['index', 'details'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index','details'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadCallExpertController',
                'rules' => [
                    [
                        // 'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadController',
                'rules' => [
                    [
                        'actions' => [
                            'create', 'add-comment', 'change-state', 'unassign', 'take', 'auto-take',
                            'set-rating', 'add-note', 'unprocessed', 'call-expert', 'send-email',
                            'check-updates', 'flow-transition', 'get-user-actions', 'add-pnr', 'update2','clone',
                            'get-badges', 'sold', 'split-profit', 'split-tips','processing', 'follow-up',  'trash', 'booked',
                            'test', 'view'
                        ],
                        'allow' => true,
                        'roles' => ['agent', 'admin', 'supervision'],
                    ],

                    [
                        'actions' => ['inbox'],
                        'allow' => true,
                        'roles' => ['agent', 'admin', 'supervision'],
                    ],

                    [
                        'actions' => [
                            'pending', 'duplicate'
                        ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => [
                            'duplicate'
                        ],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => [
                            'view', 'trash', 'sold', 'flow-transition'
                        ],
                        'allow' => true,
                        'roles' => ['qa'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadFlightSegmentController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadFlowController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'export', 'duplicate', 'view', 'ajax-activity-logs'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['ajax-activity-logs'],
                        'allow' => true,
                        'roles' => ['qa'],
                    ],
                    [
                        'actions' => ['index', 'ajax-reason-list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadTaskController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['view', 'index'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LogController',
                'rules' => [
                    [
                        'actions' => ['index', 'clear', 'view', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'NotificationsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent', 'qa'],
                    ],
                ],
            ],
            [
                'controller' => 'PhoneController',
                'rules' => [
                    [
                        'actions' => ['index', 'get-token', 'test', 'ajax-phone-dial', 'ajax-save-call', 'ajax-call-redirect'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index', 'get-token', 'test', 'ajax-phone-dial', 'ajax-save-call', 'ajax-call-redirect'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ProfitBonusController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'ProjectController',
                'rules' => [
                    [
                        //'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'QuoteController',
                'rules' => [
                    [
                        'actions' => [
                            'create', 'save', 'decline', 'calc-price', 'extra-price', 'clone',
                            'send-quotes', 'get-online-quotes','get-online-quotes-old','status-log','preview-send-quotes',
                            'create-quote-from-search','preview-send-quotes-new',
                        ],

                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'QuotePriceController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'QuotesController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'ajax-details'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['ajax-details'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'QuoteStatusLogController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'export', 'duplicate'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin']
                    ],
                    [
                        'actions' => ['view', 'index', 'ajax-reason-list'],
                        'allow' => true,
                        'roles' => ['agent']
                    ]
                ]
            ],
            [
                'controller' => 'ReportController',
                'rules' => [
                    [
                        'actions' => [
                            'sold', 'view-sold'
                        ],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                    [
                        'actions' => [
                            'agents'
                        ],
                        'allow' => true,
                        'roles' => ['admin', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'SettingController',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'SettingsController',
                'rules' => [
                    [
                        'actions' => [
                            'projects', 'airlines', 'airports', 'logging', 'acl', 'email-template',
                            'sync', 'view-log', 'acl-rule', 'project-data', 'synchronization'
                        ],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
//            [
//                'controller' => 'SiteController',
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['index', 'logout', 'profile', 'get-airport', 'blank'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
            [
                'controller' => 'SmsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete'], //'delete', 'create',
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],

                    [
                        'actions' => ['index', 'view', 'inbox'], //'delete', 'create',
                        'allow' => true,
                        'roles' => ['qa'],
                    ],

                    [
                        'actions' => ['delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'SmsTemplateTypeController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'synchronization'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'SourcesController',
                'rules' => [
                    [
                        //'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'StatsController',
                'rules' => [
                    [
                        'actions' => ['index', 'call-sms', 'calls-graph', 'sms-graph', 'emails-graph'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'TaskController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['view', 'index'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ToolsController',
                'rules' => [
                    [
                        'actions' => ['clear-cache', 'supervisor'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ]
                ],
            ],
            [
                'controller' => 'UserCallStatusController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'update-status'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['index', 'update-status'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'UserConnectionController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'stats'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'UserGroupAssignController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin','userManager'], //'supervision',
                    ],
                ],
            ],
            [
                'controller' => 'UserGroupController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin','userManager'], //'supervision',
                    ],
                ],
            ],
            [
                'controller' => 'UserParamsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'UserProjectParamsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view', 'create-ajax', 'update-ajax'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin','userManager'],
                    ],
                ],
            ],

        ];

//        VarDumper::dump($array);die;

        foreach ($array as $arrayItem) {
            foreach ($arrayItem['rules'] as $key => $item) {
                if ($item['allow']) {
                    foreach ($item['roles'] as $role) {
                        $roles[$role][] = [
                            'controller' => $arrayItem['controller'],
                            'actions' => $item['actions']??['*']
                        ];
                    }
                }
            }
        }

//        VarDumper::dump($roles);

        $batchTmpTableItem = [];
        $batchTmpTableItemChild = [];

        $str = '<table border="3" cellpadding="10" cellspacing="5">';
        $str .= '<tr><td>Role</td><td>Controller</td><td>Actions</td><td>Path</td></tr>';
        foreach ($roles as $role => $item) {
            foreach ($item as $element) {
                $actions = $element['actions'];
                $controller = $element['controller'];
                $str .= '<tr>';
                $str .= '<td>' . $role . '</td>';
                $str .= '<td>' .$controller . '</td>';
                $str .= '<td>' . ($actions ? implode('<br>', $actions) : '') . '</td>';
                $str .= '<td>' . ($actions ? implode('<br>', $this->getPathForTable($actions, $controller, $batchTmpTableItem, $batchTmpTableItemChild, $role)) : ''). '</td>';
                $str .= '</tr>';
            }
        }
        $str .= '</table>';

        ksort($batchTmpTableItem);
        ksort($batchTmpTableItemChild);

//        echo count($batchTmpTableItem);
//        echo count($batchTmpTableItemChild);

//        VarDumper::dump($batchTmpTableItem);
//        VarDumper::dump($batchTmpTableItemChild);
//        die;

        $batchTableItem = [];
        $batchTableItemChild = [];
        foreach ($batchTmpTableItem as $key => $item) {
            $batchTableItem[] = [$key, $batchTmpTableItem[$key]];
        }
        foreach ($batchTmpTableItemChild as $key => $item) {
            $batchTableItemChild[] = $batchTmpTableItemChild[$key];
        }
//        VarDumper::dump($batchTableItem);
//        VarDumper::dump($batchTableItemChild);die;
        Yii::$app->db->createCommand()->batchInsert('{{%auth_item}}', ['name', 'type'], $batchTableItem)->execute();
        Yii::$app->db->createCommand()->batchInsert('{{%auth_item_child}}', ['child', 'parent'], $batchTableItemChild)->execute();
        return $str;

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

        if($mail->send()) {
            echo 'Send';
        } else {
            echo 'Not send';
        }
    }


    public function actionComPreview()
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
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


        Notifications::create(Yii::$app->user->id, 'Test '.date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);

        $socket = 'tcp://127.0.0.1:1234';
        $user_id = Yii::$app->user->id; //'tester01';
        $lead_id = 12345;
        $data['message'] = 'test '.date('H:i:s');
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


    private function generateWebsocketKey() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';
        $key = '';
        $chars_length = strlen($chars);
        for ($i = 0; $i < 16; $i++) $key .= $chars[mt_rand(0, $chars_length-1)];
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
        if(isset($matches[1]) && $matches[1]) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                if(isset($messageArr[2]) && $messageArr[2]) {
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


        for($i = 0; $i<=20; $i++) {

            echo \Yii::t('app', '{n, selectordinal,
     =0{У вас нет новых сообщений}

     one{У вас # непрочитанное сообщение}
     few{У вас # непрочитанных сообщения}
     many{У вас # непрочитанных сообщений...}
     other{У вас # прочитанных сообщений!}}',
                ['n' => $i]
            ).'<br>';
        }

    }

    public function actionEmailJob()
    {

        $job = new ReceiveEmailsJob();

        $job->last_email_id = 18964;

        $data = [
            'last_email_id' => 18964,
            'run_all' => 'ok',
        ];

        $job->request_data = $data;

        /** @var Queue $queue */
        $queue = \Yii::$app->queue_email_job;

        $queue->push($job);

        return 'ok';
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
        $lead_id = 54719;
        $n = 0;
        foreach ($statuses as $status) {
            sleep(random_int(5, 7));
            $n++;
            Notifications::socket(null, $lead_id, 'callUpdate', ['status' => $status, 'duration' =>  ($status == 'completed' ? random_int(51, 180) : 0), 'snr' => $n], true);
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
            Notifications::socket($user_id, $lead_id = null, 'incomingCall', $data, true);
            echo '<br>'.$status;
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
        $timezoneName = timezone_name_from_abbr('',intval($offset) * 60 * 60,0);
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
        if($timezoneName) {
            $timezone = new \DateTimeZone($timezoneName);
            $dt->setTimezone($timezone);
        }
        $clientTime =  $dt->format('H:i');

        echo $timezoneName. ' - ' . $dt->getOffset();
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

        if($user_id) {
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


}
