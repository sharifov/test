<?php

namespace console\controllers;

use Co;
use common\models\UserConnection;
use common\models\UserOnline;
use console\socket\Services\RedisChannel;
use src\helpers\app\AppHelper;
use src\model\user\entity\monitor\UserMonitor;
use src\model\user\entity\userStatus\UserStatus;
use src\services\clientChatService\ClientChatService;
use Swoole\Table;
use Swoole\WebSocket\Server;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

class WebsocketServerV3Controller extends Controller
{
    public function init()
    {
        \Yii::$app->log->flushInterval = 1;
        \Yii::$app->log->targets['file']->exportInterval = 1;
        \Yii::$app->log->targets['db-error']->exportInterval = 1;
        \Yii::$app->log->targets['db-info']->exportInterval = 1;

        if (!empty(\Yii::$app->log->targets['file-fb-error'])) {
            \Yii::$app->log->targets['file-fb-error']->exportInterval = 1;
        }
    }

    public function actionStart()
    {
        printf("\n--- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        \Yii::info(__METHOD__, 'info\ws:actionStart');

        $thisClass = $this;
        $frontendConfig = ArrayHelper::merge(
            require \Yii::getAlias('@frontend/config/main.php'),
            require \Yii::getAlias('@frontend/config/main-local.php')
        );

        if (!empty(\Yii::$app->redis)) {
            $redisConfig['host'] = \Yii::$app->redis->hostname;
            $redisConfig['port'] = \Yii::$app->redis->port;
        } else {
            $redisConfig = ['host' => '127.0.0.1', 'port' => 6379];
        }

        $tblConnections = new Table(4000);
        $tblConnections->column('fd', Table::TYPE_INT);
        $tblConnections->column('uc_id', Table::TYPE_INT);
        $tblConnections->column('uid', Table::TYPE_STRING, 30);
        $tblConnections->column('user_id', Table::TYPE_INT);
        $tblConnections->column('name', Table::TYPE_STRING, 64);
        $tblConnections->column('dt', Table::TYPE_STRING, 20);
        $tblConnections->create();

        $wsConfig = \Yii::$app->params['webSocketServer'];
        $wsHost = $wsConfig['host'] ?: 'localhost';
        $wsPort = $wsConfig['port'] ?: 8080;
        $wsMode = $wsConfig['mode'] ?: SWOOLE_PROCESS;
        $wsSockType = $wsConfig['sockType'] ?: SWOOLE_SOCK_TCP;

        $server = new Server($wsHost, $wsPort, $wsMode, $wsSockType);

        if (!empty($wsConfig['settings'])) {
            $server->set($wsConfig['settings']);
        }

        $redisSubscribe = new RedisChannel();

        $server->tblConnections = $tblConnections;
        $server->redis = null;
        $server->channelList = [];

        $server->on('start', static function (Server $server) {
            echo ' Swoole WebSocket Server is started at ' . $server->host . ':' . $server->port . PHP_EOL;

            if (!empty(\Yii::$app->params['appInstance'])) {
                $ucList = UserConnection::find()->where(['uc_app_instance' => \Yii::$app->params['appInstance']])->all();
                if ($ucList) {
                    foreach ($ucList as $item) {
                        $item->delete();
                    }
                    unset($ucList);
                }
            }
        });

        $server->on('workerStart', static function ($server, $workerId) use ($frontendConfig, $thisClass, $redisConfig, $redisSubscribe) {
            echo ' Websocket Worker (Id: ' . $workerId . ')  start: ' . date('Y-m-d H:i:s') . PHP_EOL;
            \Yii::info('Websocket Worker (Id: ' . $workerId . ')  start: ' . date('Y-m-d H:i:s'), 'info\ws:actionStart:event:workerStart');


            $server->tick(20000, static function () use ($server) {
                if (!empty($server->tblConnections)) {
                    foreach ($server->tblConnections as $connection) {
                        // $server->push($connection['fd'], json_encode(['cmd' => 'pong', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                        try {
                            $server->push($connection['fd'], 'ping', WEBSOCKET_OPCODE_PING);
                        } catch (\Throwable $e) {
                            \Yii::error(AppHelper::throwableLog($e, true), 'ws:workerStart:tick20000');
                        }
                    }
                }
            });

            $server->tick(30000, static function () use ($server) {
                try {
                    \Yii::$app->db->createCommand('SELECT 1')->execute();
                } catch (\Throwable $e) {
                    \Yii::error(ArrayHelper::merge(
                        ['msg' => 'Check isActive DB connection'],
                        AppHelper::throwableLog($e, true)
                    ), 'ws:workerStart:tick30000');
                    if (strpos($e->getMessage(), 'server has gone away') !== false) {
                        try {
                            \Yii::$app->db->close();
                            \Yii::$app->db->open();
                            \Yii::$app->db->createCommand('SELECT 1')->execute();
                            \Yii::info(['message' => 'DB connection reopened', 'connection' => 'DB'], 'info\ws:dbConnectionReopened');
                        } catch (\Throwable $t) {
                            \Yii::error(ArrayHelper::merge(
                                ['msg' => 'Reopen DB connection'],
                                AppHelper::throwableLog($e, true)
                            ), 'ws:workerStart:tick30000:reopenConnection');
                        }
                    }
                }
            });

            $server->tick(60000, static function () use ($server) {
                try {
                    \Yii::$app->db_slave->createCommand('SELECT 1')->execute();
                } catch (\Throwable $e) {
                    \Yii::error(ArrayHelper::merge(
                        ['msg' => 'Check isActive DB_SLAVE connection'],
                        AppHelper::throwableLog($e, true)
                    ), 'ws:workerStart:tick60000');
                    if (strpos($e->getMessage(), 'server has gone away') !== false) {
                        try {
                            \Yii::$app->db_slave->close();
                            \Yii::$app->db_slave->open();
                            \Yii::$app->db_slave->createCommand('SELECT 1')->execute();
                            \Yii::info(['message' => 'DB connection reopened', 'connection' => 'DB_SLAVE'], 'info\ws:dbConnectionReopened');
                        } catch (\Throwable $t) {
                            \Yii::error(ArrayHelper::merge(
                                ['msg' => 'Reopen DB_SLAVE connection'],
                                AppHelper::throwableLog($e, true)
                            ), 'ws:workerStart:tick60000:reopenConnection');
                        }
                    }
                }
            });

            $redis = new \Swoole\Coroutine\Redis();

            if ($redis->connect($redisConfig['host'], $redisConfig['port']) === true) {
                echo 'Redis Connected on workerStart' . $redisConfig['host'] . ':' . $redisConfig['port'] . PHP_EOL;
            }

            $server->redis = $redis;

            go(function () use ($server, $redisConfig, $redisSubscribe) {
                \Yii::info('New coroutine: start', 'info\ws:actionStart:event:open:coroutineStart');

                while (true) {
                    $redis = new \Swoole\Coroutine\Redis();

                    if ($redis->connect($redisConfig['host'], $redisConfig['port']) === true) {
                        echo ' Redis Connected on coroutine' . $redisConfig['host'] . ':' . $redisConfig['port'] . PHP_EOL;

                        $redis->subscribe($redisSubscribe->getNameList());
                    } else {
                        continue;
                    }

                    while ($msg = $redis->recv()) {
                        list($type, $name, $info) = $msg;

                        switch ($name) {
                            case RedisChannel::SUBSCRIBE_CHANNEL:
                                echo "sub {$info}" . PHP_EOL;
                                $redis->subscribe([$info]);
                                break;
                            case RedisChannel::UNSUBSCRIBE_CHANNEL:
                                echo "unsub {$info}" . PHP_EOL;
                                $redis->unsubscribe([$info]);
                                break;

                            default:
                                $channelList = $redisSubscribe->getList();

                                if ($type == 'subscribe') {
                                    echo 'sub';
                                } elseif ($type == 'unsubscribe' && $info == 0) {
                                    echo 'unsubscribe';
                                } elseif ($type == 'message') {
                                    if (!empty($channelList[$name])) {
                                        foreach ($channelList[$name] as $fd) {
                                            try {
                                                $server->push($fd, $info);
                                            } catch (\Throwable $e) {
                                                echo ': Error: ' . $e->getMessage() . ' Date: ' . date('m-d H:i:s') . PHP_EOL;
                                                \Yii::error([
                                                    'message' => $e->getMessage(),
                                                    'result' => VarDumper::dumpAsString($msg),
                                                    'exception' => AppHelper::throwableLog($e, true)
                                                ], 'ws:workerStart:message:server:push');
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            });
        });

        $server->on('request', static function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            $response->header('Content-Type', 'application/json');

            $result = [
                [
                    'app' => 'app',
                    'type' => 'component',
                    'service' => 'db',
                    'status' => \Yii::$app->applicationStatus->dbStatus()
                ],
                [
                    'app' => 'app',
                    'type' => 'component',
                    'service' => 'db_slave',
                    'status' => \Yii::$app->applicationStatus->dbSlaveStatus()
                ],
                [
                    'app' => 'app',
                    'type' => 'component',
                    'service' => 'db_postgres',
                    'status' => \Yii::$app->applicationStatus->dbPostgresStatus()
                ],
                [
                    'app' => 'app',
                    'type' => 'component',
                    'service' => 'redis',
                    'status' => \Yii::$app->applicationStatus->redisStatus()
                ]
            ];

            $notWorkingComponentsList = array_filter($result, function ($item) {
                return isset($item['status']) && $item['status'] !== 'ok';
            });

            if (count($notWorkingComponentsList) > 0) {
                $response->status(500);
            }
            $response->end(Json::encode($result));
        });

        $server->on('open', static function (Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass, $redisConfig, $redisSubscribe) {
            echo '+ ' . date('m-d H:i:s') . " +{$request->fd}";

            try {
                if (isset($request->header['health-check'])) {
                    echo PHP_EOL . ' Health check ';
                    $mysqlStatus = 'Ok';
                    try {
                        \Yii::$app->db->createCommand('SELECT 1')->execute();
                    } catch (\Throwable $e) {
                        \Yii::error([
                            'message' => $e->getMessage(),
                        ], 'ws:open:health-check:db');
                        $mysqlStatus = 'Error';
                    }
                    $mysqlSlaveStatus = 'Ok';
                    try {
                        \Yii::$app->db_slave->createCommand('SELECT 1')->execute();
                    } catch (\Throwable $e) {
                        \Yii::error([
                            'message' => $e->getMessage(),
                        ], 'ws:open:health-check:db_slave');
                        $mysqlSlaveStatus = 'Error';
                    }
                    try {
                        // SWOOLE_REDIS_STATE_READY === 3 ?
                        $redisStatus = ($server->redis->getState() === 3) ? 'Ok' : 'Error';
                    } catch (\Throwable $e) {
                        \Yii::error([
                            'message' => $e->getMessage(),
                        ], 'ws:open:health-check:redis');
                        $redisStatus = 'Error';
                    }
                    $result = json_encode([
                        'ws' => 'Ok',
                        'message' => 'Successfully connected to websocket server',
                        'appInstance' => \Yii::$app->params['appInstance'],
                        'mysql' => $mysqlStatus,
                        'mysqlSlave' => $mysqlSlaveStatus,
                        'redis' => $redisStatus,
                    ]);
                    echo $result . PHP_EOL;
                    $server->push($request->fd, $result);
                    return;
                }
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ws:healthCheck');
            }

            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {
                $userId = $user->getId();

                try {
                    $server->push($request->fd, json_encode(['cmd' => 'userInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:userInit');
                }

                $ua = !empty($request->header['user-agent']) ? substr($request->header['user-agent'], 0, 255) : null;
                $ip = empty($request->get['ip']) ? null : substr($request->get['ip'], 0, 40); //!empty($request->server['remote_addr']) ? substr($request->server['remote_addr'], 0, 40) : null;
                $subList = empty($request->get['sub_list']) || !is_array($request->get['sub_list']) ? [] : $request->get['sub_list'];

                $uid = uniqid('', false);

                $userConnection = new UserConnection();
                $userConnection->uc_connection_uid = $uid;
                $userConnection->uc_connection_id = $request->fd;
                $userConnection->uc_case_id = empty($request->get['case_id']) ? null : (int) $request->get['case_id'];
                $userConnection->uc_lead_id = empty($request->get['lead_id']) ? null : (int) $request->get['lead_id'];
                $userConnection->uc_controller_id = empty($request->get['controller_id']) ? null : substr($request->get['controller_id'], 0, 50);
                $userConnection->uc_action_id = empty($request->get['action_id']) ? null : substr($request->get['action_id'], 0, 50);
                $userConnection->uc_page_url = empty($request->get['page_url']) ? null : substr($request->get['page_url'], 0, 1400);
                $userConnection->uc_user_agent = $ua;
                $userConnection->uc_ip = $ip;
                $userConnection->uc_user_id = $userId;
                $userConnection->uc_app_instance = \Yii::$app->params['appInstance'] ?? null;
                $userConnection->uc_sub_list = $subList ? @json_encode($subList) : null;
                $userConnection->uc_idle_state = false;

                try {
                    if ($userConnection->save()) {
                        $userOnline = UserOnline::find()->where(['uo_user_id' => $userConnection->uc_user_id])->one();
                        if (!$userOnline) {
                            $uo = new UserOnline();
                            $uo->uo_user_id = $userConnection->uc_user_id;
                            $uo->uo_idle_state = false;
                            $uo->uo_idle_state_dt = date('Y-m-d H:i:s');

                            try {
                                if ($uo->save()) {
                                    UserMonitor::addEvent($uo->uo_user_id, UserMonitor::TYPE_ONLINE);
                                    UserMonitor::addEvent($uo->uo_user_id, UserMonitor::TYPE_ACTIVE);

                                    try {
                                        ClientChatService::createJobAssigningUaToPendingChats((int)$uo->uo_user_id);
                                        \Yii::$app->db->createCommand()->update(UserStatus::tableName(), ['us_phone_ready_time' => time()], ['us_user_id' => $userId, 'us_call_phone_status' => 1])->execute();
                                    } catch (\Throwable $e) {
                                        \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:UserStatus:update');
                                    }
                                } else {
                                    echo 'Error: UserOnline:save' . PHP_EOL;
                                    \Yii::error([
                                        'model' => $uo->getAttributes(),
                                        'errors' => $uo->getErrors(),
                                    ], 'ws:open:UserOnline:save');
                                }
                            } catch (\Throwable $e) {
                                \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:uo:save');
                                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                                    $userOnline = UserOnline::find()->where(['uo_user_id' => $userConnection->uc_user_id])->one();
                                    if ($userOnline && $userOnline->uo_idle_state) {
                                        $userOnline->uo_idle_state = false;
                                        $userOnline->uo_idle_state_dt = date('Y-m-d H:i:s');
                                        $userOnline->update();
                                    }
                                }
                            }
                            unset($uo);
                        } else {
                            if ($userOnline->uo_idle_state) {
                                $userOnline->uo_idle_state = false;
                                $userOnline->uo_idle_state_dt = date('Y-m-d H:i:s');
                                $userOnline->update();
                            }
                        }
                        unset($userOnline);
                    } else {
                        echo 'Error: UserConnection:save' . PHP_EOL;
                        \Yii::error([
                            'model' => $userConnection->getAttributes(),
                            'errors' => $userConnection->getErrors(),
                        ], 'ws:open:UserConnection:save');
                        // VarDumper::dump($userConnection->errors);
                    }
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:userConnection:save');
                }

                try {
                    $server->tblConnections->set($request->fd, [
                        'fd' => $request->fd,
                        'uc_id' => $userConnection->uc_id,
                        'uid' => $uid,
                        'user_id' => $userId,
                        'name' => $user->username,
                        'dt' => date('Y-m-d H:i:s'),
                        //'sub_list' => $userConnection->uc_sub_list
                    ]);
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:tblConnections:set');
                }

                echo ': ' . $user->username . ' (' . $userId . ')' . PHP_EOL;

                unset($user);

                $json = json_encode(['cmd' => 'initConnection', 'fd' => $userConnection->uc_connection_id, 'uc_id' => $userConnection->uc_id]);
                try {
                    $server->push($request->fd, $json); //WEBSOCKET_OPCODE_PING
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:InitConnection:push');
                }

                if ($subList) {
                    foreach ($subList as $k => $value) {
                        if (strpos($value, 'user-') !== false) {
                            unset($subList[$k]);
                        }
                    }
                }

                $subList[] = 'user-' . $userId;
                $subList[] = 'con-' . $userConnection->uc_id;

                foreach ($subList as $channel) {
                    $redisSubscribe->add($channel, $request->fd);
                }

                unset($userConnection);
            } else {
                echo ' : not init user' . PHP_EOL;
                try {
                    $server->push($request->fd, json_encode(['cmd' => 'userNotInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                    $server->disconnect($request->fd, 403, 'Access denied');
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ws:open:notInitUser');
                }
            }
        });

        $server->on('message', static function (Server $server, \Swoole\WebSocket\Frame $frame) use ($thisClass) {
            echo ' * ' . date('m-d H:i:s') . " received message: {$frame->data}\n";

            try {
                $data = json_decode($frame->data, true);
                $dataRequest = $thisClass->dataProcessing($server, $frame, $data);
                if ($dataRequest) {
                    $server->push($frame->fd, json_encode($dataRequest));
                }
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ws:message');
            }
        });

        $server->on('close', static function (Server $server, int $fd) use ($redisSubscribe) {
            echo '- ' . date('m-d H:i:s') . " -{$fd}\n";
            try {
                $row = $server->tblConnections->get($fd);
                $server->tblConnections->del($fd);

                if (!empty($row['uid'])) {
                    $uc = UserConnection::find()->where(['uc_connection_uid' => $row['uid']])->limit(1)->one();
                    if ($uc) {
                        if (!empty($uc->uc_sub_list)) {
                            $subList = @json_decode($uc->uc_sub_list);
                        } else {
                            $subList = [];
                        }

                        $subList[] = 'user-' . $row['user_id'];
                        $subList[] = 'con-' . $row['uc_id'];

                        foreach ($subList as $value) {
                            $redisSubscribe->remove($value, $fd);
                        }

                        $uc->delete();
                        unset($uc);

                        UserMonitor::closeConnectionEvent($row['user_id']);
                    }
                }
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ws:close');
            }
        });

        $server->on('workerError', static function (Server $server, int $workerId, $workerPid, $exitCode, $signal) {
            $message = "Error Worker (Id: {$workerId}): pid={$workerPid} code={$exitCode} signal={$signal}";
            echo '> ' . $message . PHP_EOL;
            \Yii::error(['message' => 'Error Worker', 'workerId' => $workerId, 'workerPid' => $workerPid, 'exitCode' => $exitCode, 'signal' => $signal], 'ws:workerError');
        });

        $server->start();
    }

    public function actionNumberOfCoroutines()
    {
        echo count(Co::listCoroutines()) . PHP_EOL;
    }

    /**
     * @param Server $server
     * @param \Swoole\WebSocket\Frame $frame
     * @param array $data
     * @return array|null
     */
    public function dataProcessing(Server $server, \Swoole\WebSocket\Frame $frame, array $data): ?array
    {
        $out = null;

        if (!empty($data['ping'])) {
            return [
                'pong' => $data['ping'],
                'appInstance' => \Yii::$app->params['appInstance'],
            ];
        }

        if (empty($data['c'])) {
            $out['errors'][] = 'Error: Not isset "c" param';
        }

        if (empty($data['a'])) {
            $out['errors'][] = 'Error: Not isset "a" param';
        }

        if (empty($data['p'])) {
            $out['errors'][] = 'Error: Not isset "p" param';
        }

        if (!empty($out['errors'])) {
            \Yii::warning($out['errors'], 'ws:dataProcessing');
            return $out;
        }

        $controller = (string)$data['c'];
        $action = (string)$data['a'];
        $params = $data['p'];

        //$out['data'] = print_r($params, true);

        if ($controller === 'idle' && $action === 'set') {
            if (isset($params['val'])) {
                $val = (bool) $params['val'];

                UserConnection::updateAll(
                    ['uc_idle_state' => $val, 'uc_idle_state_dt' => date('Y-m-d H:i:s')],
                    ['uc_connection_id' => $frame->fd]
                );
                // 'uc_app_instance' => \Yii::$app->params['appInstance']
                UserOnline::updateIdleState($frame->fd, $val);
                unset($val, $params['val']);
            }
        }


//        if ($controller === 'window' && $action === 'set') {
//            if (isset($params['val'])) {
//                $val = (bool) $params['val'];
//                UserConnection::updateAll(['uc_window_state' => $val, 'uc_window_state_dt' => date('Y-m-d H:i:s')], ['uc_connection_id' => $frame->fd, 'uc_app_instance' => \Yii::$app->params['appInstance']]);
//                unset($val);
//            }
//        }

        if ($controller === 'info' && $action === 'get') {
            $out['data'] = $data;
            $out['connection_info'] = $server->connection_info($frame->fd);
            //$data['client_info'] = $server->getClientInfo($frame->fd);
            $out['connection_list'] = $server->connection_list();
            $out['dt'] = date('Y-m-d H:i:s');

            unset($data);
        }

        if ($controller === 'server' && $action === 'info') {
            $out['Requested'] = round(memory_get_usage() / 1024, 2) . ' KB';
            $out['Allocated'] = round(memory_get_usage(true) / 1024, 2) . ' KB';
            $out['Peak requested'] = round(memory_get_peak_usage() / 1024, 2) . ' KB';
            $out['Peak allocated'] = round(memory_get_peak_usage(true) / 1024, 2) . ' KB';
            $out['dt'] = date('Y-m-d H:i:s');
        }

        if ($controller = $this->resolveController($controller, $action)) {
            try {
                $row = $server->tblConnections->get($frame->fd);
                $connectionIdentity = null;
                if (!empty($row['uc_id'])) {
                    $connectionIdentity = (int)$row['uc_id'];
                }
                $out = $controller($connectionIdentity, $params);
            } catch (\Throwable $e) {
                $out ['errors'][] = $e->getMessage();
                \Yii::error($e->getMessage(), 'ws:dataProcessing:resolveController');
            }
            unset($controller);
        }

        return $out;
    }


    /**
     * @param string $controllerName
     * @param string $actionName
     * @return callable|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function resolveController(string $controllerName, string $actionName): ?callable
    {
        $controllerClass = '\console\socket\controllers' . '\\' . $controllerName . 'Controller';
        if (class_exists($controllerClass)) {
            $controller = \Yii::$container->get($controllerClass);
            if (method_exists($controller, 'action' . $actionName)) {
                return [$controller, 'action' . $actionName];
            }
        }
        return null;
    }

    /**
     * @param \Swoole\Http\Request $request
     * @param array $frontendConfig
     * @return IdentityInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    private function getIdentityByCookie(\Swoole\Http\Request $request, array $frontendConfig): ?IdentityInterface
    {
        try {
            $cookieName = \Yii::$app->params['wsIdentityCookie']['name'] ?? '';
            $cookieValue = $request->cookie[$cookieName] ?? null;

            $identityClass = $frontendConfig['components']['user']['identityClass'] ?? '';
            $cookieValidationKey = $frontendConfig['components']['request']['cookieValidationKey'] ?? '';

            $dataCookie = \Yii::$app->getSecurity()->validateData($cookieValue, $cookieValidationKey);

            if ($dataCookie) {
                $data = @unserialize($dataCookie, ['allowed_classes' => false]);
                if (is_array($data) && isset($data[0], $data[1]) && $data[0] === $cookieName) {
                    $data = json_decode($data[1], true);

                    //\yii\helpers\VarDumper::dump($data);

                    if (is_array($data) && count($data) == 3) {
                        list($id, $authKey, $duration) = $data;
                        /* @var $class IdentityInterface */
                        $class = $identityClass;
                        $identity = $class::findIdentity($id);
                        if ($identity !== null) {
                            if (!$identity instanceof IdentityInterface) {
                                ///throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
                                \Yii::error("$class::findIdentity() must return an object implementing IdentityInterface.", 'WebSocketServer:IdentityInterface:' . __METHOD__);
                                echo "$class::findIdentity() must return an object implementing IdentityInterface.";
                            } elseif (!$identity->validateAuthKey($authKey)) {
                                \Yii::warning("Invalid auth key attempted for user '$id': $authKey", 'WebSocketServer:validateAuthKey:' . __METHOD__);
                                echo "Invalid auth key attempted for user '$id': $authKey";
                            } else {
                                return $identity;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'ws:getIdentityByCookie');
        }

        return null;
    }
}
