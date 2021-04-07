<?php

namespace console\controllers;

use common\models\UserCallStatus;
use common\models\UserConnection;
use common\models\UserOnline;
use console\socket\Commands\Connectionable;
use console\socket\Commands\Serverable;
use sales\auth\Auth;
use sales\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use sales\model\user\entity\monitor\UserMonitor;
use Swoole\Redis;
use Swoole\Table;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * Class WebsocketServerController
 * @package console\controllers
 */
class WebsocketServerController extends Controller
{

    /**
     *
     */
    public function actionStart()
    {
        printf("\n--- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

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

        $redisList = [];

//        $redis2 = new \Swoole\Coroutine\Redis();
//        $redis2->connect("localhost", 6379);

        $tblConnections = new Table(4000);
        $tblConnections->column('fd', Table::TYPE_INT);
        $tblConnections->column('uc_id', Table::TYPE_INT);
        $tblConnections->column('uid', Table::TYPE_STRING, 30);
        $tblConnections->column('user_id', Table::TYPE_INT);
        $tblConnections->column('name', Table::TYPE_STRING, 64);
        $tblConnections->column('dt', Table::TYPE_STRING, 20);
        //$tblConnections->column('sub_list', Table::TYPE_STRING, 255);
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

        $server->on('workerStart', static function ($server, $workerId) use ($frontendConfig, $thisClass, $redisConfig) {
            echo ' Worker (Id: ' . $workerId . ')  start: ' . date('Y-m-d H:i:s') . PHP_EOL;


            $server->tick(20000, static function () use ($server) {
                if (!empty($server->tblConnections)) {
                    foreach ($server->tblConnections as $connection) {
                        // $server->push($connection['fd'], json_encode(['cmd' => 'pong', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                        $server->push($connection['fd'], 'ping', WEBSOCKET_OPCODE_PING);
                    }
                }
            });

            $server->tick(30000, static function () use ($server) {
                \Yii::$app->db->createCommand('SELECT 1')->execute();
            });


            $client = new \swoole_redis();

            $server->redis = $client;

            $client->on('message', static function (Redis $redis, $result) use ($server, $workerId) {
                echo 'message to client';
                if ($result) {
                    [$cmd, $channel, $value] = $result;
//                    if ($cmd === 'subscribe') {
//
//                    }
//
//                    if ($cmd === 'unsubscribe') {
//
//                    }
                    if ($cmd === 'message') {
                        if (!empty($server->channelList[$channel])) {
                            foreach ($server->channelList[$channel] as $fd) {
                                try {
                                    $server->push($fd, $value);
                                } catch (\Throwable $e) {
                                    echo 'Error: ' . $e->getMessage();
                                }
                            }
                        }
                    }
                }
            });

            $client->connect($redisConfig['host'], $redisConfig['port'], static function (\swoole_redis $client, $result) use ($redisConfig) {
                echo ' Redis Connected ' . $redisConfig['host'] . ':' . $redisConfig['port'] . PHP_EOL;
            });
        });

        $server->on('open', static function (Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass, $redisConfig, $redisList) {
            echo '+ ' . date('m-d H:i:s') . " +{$request->fd}";
            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {
                $userId = $user->getId();

                $server->push($request->fd, json_encode(['cmd' => 'userInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING

//                $server->tick(30000, static function() use ($server, $request) {
//                    //$server->push($request->fd, json_encode(['cmd' => 'pong', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
//                    $server->push($request->fd, 'ping', WEBSOCKET_OPCODE_PING); //WEBSOCKET_OPCODE_PING
//                });




                //VarDumper::dump($request);
                //VarDumper::dump($request->get);

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
                $userConnection->uc_page_url = empty($request->get['page_url']) ? null : substr($request->get['page_url'], 0, 500);
                $userConnection->uc_user_agent = $ua;
                $userConnection->uc_ip = $ip;
                $userConnection->uc_user_id = $userId;
                $userConnection->uc_app_instance = \Yii::$app->params['appInstance'] ?? null;
                $userConnection->uc_sub_list = $subList ? @json_encode($subList) : null;
                //$userConnection->uc_idle_state = false;

                if ($userConnection->save()) {
//                    $um = UserMonitor::find()->where(['um_user_id' => $userId, 'um_type_id' => UserMonitor::TYPE_ONLINE])->limit(1)->orderBy(['um_id' => SORT_DESC])->one();
//                    if (!$um) {
//                        UserMonitor::addEvent($userId, UserMonitor::TYPE_ONLINE);
//                    }

                    $userOnline = UserOnline::find()->where(['uo_user_id' => $userConnection->uc_user_id])->one();
                    if (!$userOnline) {
                        $uo = new UserOnline();
                        $uo->uo_user_id = $userConnection->uc_user_id;
                        $uo->uo_idle_state = false;
                        $uo->uo_idle_state_dt = date('Y-m-d H:i:s');

                        if ($uo->save()) {
                            UserMonitor::addEvent($uo->uo_user_id, UserMonitor::TYPE_ONLINE);
                            UserMonitor::addEvent($uo->uo_user_id, UserMonitor::TYPE_ACTIVE);
                        } else {
                            echo 'Error: UserOnline:save' . PHP_EOL;
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
                    // VarDumper::dump($userConnection->errors);
                }

                $server->tblConnections->set($request->fd, [
                    'fd' => $request->fd,
                    'uc_id' => $userConnection->uc_id,
                    'uid' => $uid,
                    'user_id' => $userId,
                    'name' => $user->username,
                    'dt' => date('Y-m-d H:i:s'),
                    //'sub_list' => $userConnection->uc_sub_list
                ]);

//                foreach($server->tblConnections as $row)
//                {
//                    VarDumper::dump($row);
//                }

//                VarDumper::dump(['fd' => $request->fd,
//                    'uid' => $uid,
//                    'user_id' => $userId,
//                    'name' => $user->username,
//                    'dt' => date('Y-m-d H:i:s')]);

                echo ': ' . $user->username . ' (' . $userId . ')' . PHP_EOL;

                unset($user);




                $json = json_encode(['cmd' => 'initConnection', 'fd' => $userConnection->uc_connection_id, 'uc_id' => $userConnection->uc_id]);
                $server->push($request->fd, $json); //WEBSOCKET_OPCODE_PING



                if ($subList) {
                    foreach ($subList as $k => $value) {
                        if (strpos($value, 'user-') !== false) {
                            unset($subList[$k]);
                        }
                    }
                }

                $subList[] = 'user-' . $userId;
                $subList[] = 'con-' . $userConnection->uc_id;


                foreach ($subList as $value) {
                    $server->channelList[$value][$request->fd] = $request->fd;
                    $server->redis->subscribe($value);
                }

                unset($userConnection);

            //VarDumper::dump($server->channelList);

                //$server->redis->subscribe('con-' . $userConnection->uc_id);
                //$server->redis->subscribe('con-' . $userConnection->uc_id);

//                $client = new \swoole_redis();
//
////                $redis->on('message', static function (Redis $redis, $result) use ($server, $request) {
////
////                    if (!empty($result[0]) && $result[0] === 'message') {
////                        // echo 'mes: ' . $msg[2] . PHP_EOL;
////                        $server->push($request->fd, $result[2]); //WEBSOCKET_OPCODE_PING
////                    }
////
//////                    var_dump($result);
//////                    static $more = false;
//////                    if (!$more and $result[0] == 'message')
//////                    {
//////                        echo "subscribe new channel\n";
//////                        $redis->subscribe('msg_1', 'msg_2');
//////                        $redis->unsubscribe('msg_0');
//////                        $more = true;
//////                    }
////                });
//
//                $client->connect("127.0.0.1", 6379, function (\swoole_redis $client, $result) use ($server) {
//                    $client->subscribe("msg_queue", "asdasdasd");
//                });

//                $redis->connect($redisConfig['host'], $redisConfig['port'], static function (\swoole_redis $redis, $result) use ($subList) {
//                    echo "connect\n";
//
//                    $redis->subscribe('user-123');
//
////                    if ($subList) {
////                        foreach ($subList as $value) {
////                            $redis->subscribe($value);
////                            echo '* subscribe to ' .$value. "\n";
////                        }
////                    }
//                });


                //$redis = new \Swoole\Coroutine\Redis();

                //$redis->connect($redisConfig['host'], $redisConfig['port']);
                //$val = $redis->get('key');



                //$msg = $redis->subscribe($subList);

                //$redisList[$request->fd] = $redis;

//                $redis->discard();

//                while ($msg = $redis->recv())
//                {
//                    //VarDumper::dump($msg);
//                    if (!empty($msg[0]) && $msg[0] === 'message') {
//                        // echo 'mes: ' . $msg[2] . PHP_EOL;
//                        $server->push($request->fd, $msg[2]); //WEBSOCKET_OPCODE_PING
//                    }
//                }

                //$redis->discard();
            } else {
                echo ' : not init user' . PHP_EOL;
                $server->push($request->fd, json_encode(['cmd' => 'userNotInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                $server->disconnect($request->fd, 403, 'Access denied');
            }
        });


        $server->on('message', static function (Server $server, \Swoole\WebSocket\Frame $frame) use ($thisClass) {
            echo ' * ' . date('m-d H:i:s') . " received message: {$frame->data}\n";

            $data = json_decode($frame->data, true);
            $dataRequest = $thisClass->dataProcessing($server, $frame, $data);
            if ($dataRequest) {
                $server->push($frame->fd, json_encode($dataRequest));
            }
        });

        $server->on('close', static function (Server $server, int $fd) {
            echo '- ' . date('m-d H:i:s') . " -{$fd}\n";
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
                        if (isset($server->channelList[$value][$fd])) {
                            unset($server->channelList[$value][$fd]);

                            if (isset($server->channelList[$value]) && empty($server->channelList[$value])) {
                                unset($server->channelList[$value]);
                                $server->redis->unsubscribe($value);
                            }
                        }
                    }

                    $uc->delete();
                    unset($uc);

                    UserMonitor::closeConnectionEvent($row['user_id']);
                    //$userOnline = UserOnline::find()->where(['uo_user_id' => $row['user_id']])->one();
                }

//                $subQuery = UserConnection::find()->select(['DISTINCT(uc_user_id)']);
//                $userOnlineForDelete = UserOnline::find()->where(['NOT IN', 'uo_user_id', $subQuery])->all();
//
//                unset($subQuery);
//
//                if ($userOnlineForDelete) {
//                    foreach ($userOnlineForDelete as $item) {
//                        $item->delete();
//                    }
//                }
//                unset($userOnlineForDelete);

                //\Yii::$app->db->createCommand('DELETE FROM user_online WHERE uo_user_id NOT IN (SELECT DISTINCT(uc_user_id) FROM user_connection)')->execute();
            }


            //UserOnline::deleteAll('uo_user_id NOT IN (SELECT DISTINCT(uc_user_id) FROM user_connection)');

            //VarDumper::dump($server->channelList);


//            if (!empty($redisList[$fd])) {
//
//                //$subList[] = 'user-' . $userId;
//                $subList[] = 'con-' . $row['uc_id'];
//
//                //$redisList[$fd]->unsubscribe($subList);
//                //$redisList[$fd]->discard();
//
//                unset($redisList[$fd]);
//            }
        });


        $server->on('workerError', static function (Server $server, int $workerId, $workerPid, $exitCode, $signal) {
            $message = "Error Worker (Id: {$workerId}): pid={$workerPid} code={$exitCode} signal={$signal}";
            echo '> ' . $message . PHP_EOL;
        });

        $server->start();
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
            return $out;
        }

        $controller = (string)$data['c'];
        $action = (string)$data['a'];
        $params = $data['p'];

        //$out['data'] = print_r($params, true);

        if ($controller === 'idle' && $action === 'set') {
            if (isset($params['val'])) {
                $val = (bool) $params['val'];

                UserConnection::updateAll(['uc_idle_state' => $val, 'uc_idle_state_dt' => date('Y-m-d H:i:s')], ['uc_connection_id' => $frame->fd, 'uc_app_instance' => \Yii::$app->params['appInstance']]);

                //echo "\r\n";
//                $uc = UserConnection::find()->where(['uc_connection_id' => $frame->fd, 'uc_app_instance' => \Yii::$app->params['appInstance']])->one();
//                if ($uc && $uc->uc_user_id) {
//                    if ($val) {
//                        UserMonitor::setUserIdle($uc->uc_user_id);
//                    } else {
//                        UserMonitor::setUserActive($uc->uc_user_id);
//                    }

//                    UserMonitor::updateGlobalIdle($uc->uc_user_id);

//                    print_r($uc->attributes);
//                    $uc->uc_idle_state = $val;
//                    $uc->uc_idle_state_dt = date('Y-m-d H:i:s');
//                    $uc->save();
//                }

//                unset($uc, $val);
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
                $out = $controller($params);
            } catch (\Throwable $e) {
                $out ['errors'][] = $e->getMessage();
            }
            unset($controller);
        }

        return $out;
    }

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
     * @param $channel
     * @param string $message
     * @param int $repeat
     */
    public function actionPublish($channel, $message = 'Hello', $repeat = 1): void
    {
        $redis = \Yii::$app->redis;
        if ($channel) {
            for ($i = 1; $i <= $repeat; $i++) {
                $redis->publish($channel, $message . $i);
            }
            echo '- channel: ' . $channel . ', Message: ' . $message;
        }
    }

    public function actionTest(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        if (!class_exists('\Swoole\WebSocket\Server')) {
            printf("- Error: %s\n", $this->ansiFormat('Class \Swoole\WebSocket\Server - NO', Console::FG_RED));
        } else {
            printf("- OK: %s\n", $this->ansiFormat('Class \Swoole\WebSocket\Server', Console::FG_BLUE));
        }

        if (!class_exists('\Swoole\Coroutine\Redis')) {
            printf("- Error: %s\n", $this->ansiFormat('Class \Swoole\Coroutine\Redis - NO', Console::FG_RED));
        } else {
            printf("- OK: %s\n", $this->ansiFormat('Class \Swoole\Coroutine\Redis', Console::FG_BLUE));
        }

        if (!class_exists('\Swoole\Redis')) {
            printf("- Error: %s\n", $this->ansiFormat('Class \Swoole\Redis - NO', Console::FG_RED));
        } else {
            printf("- OK: %s\n", $this->ansiFormat('Class \Swoole\Redis', Console::FG_BLUE));
        }
    }

    public function actionRedis(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $client = new \swoole_redis();

        $server = null;

        $client->on("message", function (\swoole_redis $client, $data) use ($server) {
            // process data, broadcast to websocket clients
//            if ($result[0] == 'message') {
//                foreach($server->connections as $fd) {
//                    $server->push($fd, $result[1]);
//                }
//            }
        });
        $client->connect("127.0.0.1", 6379, function (\swoole_redis $client, $result) use ($server) {
            $client->subscribe("msg_queue", "asdasdasd");
        });
    }


    /**
     * @param \Swoole\Http\Request $request
     * @param array $frontendConfig
     * @return IdentityInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    private function getIdentityByCookie(\Swoole\Http\Request $request, array $frontendConfig): ?IdentityInterface
    {
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

        return null;
    }
}
