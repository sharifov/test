<?php
namespace console\controllers;

use common\models\UserConnection;
use common\models\UserOnline;
use console\daemons\ChatServer;
use frontend\widgets\OnlineConnection;
use Swoole\Table;
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


    public function actionStart()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

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

//        $redis2 = new \Swoole\Coroutine\Redis();
//        $redis2->connect("localhost", 6379);



        $tblConnections = new Table(2048);
        $tblConnections->column('fd', Table::TYPE_INT);
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

        $server->tblConnections = $tblConnections;




//        $table = new Table(1024);
//        $table->column('id', Table::TYPE_INT);
//        $table->column('name', Table::TYPE_STRING, 64);
//        $table->column('num', Table::TYPE_FLOAT);
//        $table->create();
//
//        $table['apple'] = array('id' => 145, 'name' => 'iPhone', 'num' => 3.1415);
//        $table['google'] = array('id' => 358, 'name' => "AlphaGo", 'num' => 3.1415);
//        $table['microsoft']['name'] = "Windows";
//        $table['microsoft']['num'] = '1997.03';
//
//        var_dump($table['apple']);
//        var_dump($table['microsoft']);
//        $table['google']['num'] = 500.90;
//        var_dump($table['google']);



//        $server->on('connect', static function(Server $server, $fd, $reactor_id) {
//            $info = $server->connection_info($fd);
//            $addr = $info['remote_ip'].':'.$info['remote_port'];
//            $server->send($fd, "INFO: fd=$fd, reactor_id=$reactor_id, addr=$addr\n");
//        });

        $server->on('start', static function (Server $server) {
            echo '- Swoole WebSocket Server is started at ' . $server->host.':'.$server->port . PHP_EOL;
        });

        $server->on('workerStart', static function ($server, $workerId) {

            echo '- Worker (Id: ' . $workerId . ')  start: ' . date('Y-m-d H:i:s') . PHP_EOL;




//            $client = new \Swoole\Redis;
//            $client->on('message', static function (\Swoole\Redis $client, $result) use ($server) {
//                // process data, broadcast to websocket clients
//                if ($result[0] == 'message') {
//                    foreach($server->connections as $fd) {
//                        $server->push($fd, $result[1]);
//                        echo ' -- ' . $result[1] . PHP_EOL;
//                    }
//                }
//            });
//            $client->connect('localhost', 6379, static function (\Swoole\Redis $client, $result) {
//                $client->subscribe('user-167');
//            });
        });


//        $server->on('pipeMessage', static function(\Swoole\WebSocket\Server $server, $src_worker_id, $data) {
//            echo "#{$server->worker_id} message from #$src_worker_id: $data\n";
//        });


        $server->on('open', static function(Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass, $redisConfig) {

            echo "- connection open: {$request->fd}\n";


            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {

                $userId = $user->getId();


                $server->push($request->fd, json_encode(['userInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING

                $server->tick(10000, static function() use ($server, $request) {
                    $server->push($request->fd, json_encode(['pong', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                });

                //VarDumper::dump($request);
                VarDumper::dump($request->get);

                $ua = !empty($request->header['user-agent']) ? substr($request->header['user-agent'], 0, 255) : null;
                $ip = !empty($request->server['remote_addr']) ? substr($request->server['remote_addr'], 0, 40) : null;


//                $onConnection = new UserOnline();
//                $onConnection->$userId

                $uid = uniqid('', false);

                $server->tblConnections->set($request->fd,[
                    'fd' => $request->fd,
                    'uid' => $uid,
                    'user_id' => $userId,
                    'name' => $user->username,
                    'dt' => date('Y-m-d H:i:s'),
                ]);

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

                if (!$userConnection->save()) {
                    \Yii::error(VarDumper::dumpAsString($userConnection->errors), 'WS:UserConnection:save');
                }

//
//               // VarDumper::dump($tblConnections);
//
                foreach($server->tblConnections as $row)
                {
                    VarDumper::dump($row);
                }
//
//
                unset($user);

//                $tblConnections
//
//                    $userOnline = UserOnline::find()->where(['uo_user_id' => $userId])->exists();
//                    if (!$userOnline) {
//                        $userOnline = new UserOnline();
//                        $userOnline->uo_user_id = $userId;
//                        $userOnline->save();
//                    }

                /*
                 * [header] => [
                        'upgrade' => 'websocket'
                        'connection' => 'upgrade'
                        'host' => 'localhost:8080'
                        'pragma' => 'no-cache'
                        'cache-control' => 'no-cache'
                        'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36'
                        'origin' => 'http://sales.zeit.test'
                        'sec-websocket-version' => '13'
                        'accept-encoding' => 'gzip, deflate, br'
                        'accept-language' => 'ru,en-US;q=0.9,en;q=0.8,zh;q=0.7,zh-TW;q=0.6,zh-CN;q=0.5,ko;q=0.4,de;q=0.3'
                        'sec-websocket-key' => 'Z4QFNS9V6OZJWxCw1JOKgQ=='
                        'sec-websocket-extensions' => 'permessage-deflate; client_max_window_bits'
                    ]
                    [server] => [
                        'query_string' => 'p=aaa&z=bbb'
                        'request_method' => 'GET'
                        'request_uri' => '/ws/'
                        'path_info' => '/ws/'
                        'request_time' => 1587046663
                        'request_time_float' => 1587046663.3647
                        'server_protocol' => 'HTTP/1.1'
                        'server_port' => 8080
                        'remote_port' => 38336
                        'remote_addr' => '127.0.0.1'
                        'master_time' => 1587046663
                    ]

                 */


                $redis = new \Swoole\Coroutine\Redis();

                $redis->connect($redisConfig['host'], $redisConfig['port']);
                //$val = $redis->get('key');

                $msg = $redis->subscribe(['user-' . $userId]);

                //VarDumper::dump($msg);

                while ($msg = $redis->recv())
                {
                    //VarDumper::dump($msg);
                    if (!empty($msg[0]) && $msg[0] === 'message') {
                        echo 'mes: ' . $msg[2] . PHP_EOL;
                        $server->push($request->fd, json_encode(['message' => $msg[2], 't' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                    }
                }

            } else {
                echo '- not init user' . PHP_EOL;
                $server->push($request->fd, json_encode(['userNotInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                $server->disconnect($request->fd, 403, 'Access denied');
            }


        });


        $server->on('message', static function(Server $server, \Swoole\WebSocket\Frame $frame) {
            echo "- received message: {$frame->data}\n";

            $data['connection_info'] = $server->connection_info($frame->fd);
            //$data['client_info'] = $server->getClientInfo($frame->fd);
            $data['connection_list'] = $server->connection_list();
            $cl = [];
//    foreach ($server->connections->key() as $connection) {
//        $cl[] = $connection->;
//    }

            $data['connection_list'] = $server->connections->key(); //$cl; //$server->connections;
            $data['data'] = $frame->data;
            $data['dt'] = date('Y-m-d H:i:s');



            $server->push($frame->fd, json_encode($data));
        });

        $server->on('close', static function(Server $server, int $fd) {
            echo "- connection close: {$fd}\n";
            $row = $server->tblConnections->get($fd);
            $server->tblConnections->del($fd);

            if (!empty($row['uid'])) {
                $uc = UserConnection::find()->where(['uc_connection_uid' => $row['uid']])->limit(1)->one();
                if ($uc) {
                    $uc->delete();
                }
                unset($uc);
            }

        });


        $server->on('workerError', static function(Server $server, int $workerId, $workerPid, $exitCode, $signal) {
            $message = "Error Worker (Id: {$workerId}): pid={$workerPid} code={$exitCode} signal={$signal}";
            echo '- ' . $message . PHP_EOL;
            \Yii::error($message, 'WS:'. __METHOD__);
        });

        $server->start();
    }

    /**
     * @param $userId
     * @param string $message
     */
    public function actionPublish($userId, $message = 'Hello')
    {
        /** @var \yii\redis\Connection $redis */
        $redis = \Yii::$app->redis;
        for ($i =1; $i <= 10; $i++) {
            $redis->publish('user-' . $userId, $message . $i);
        }
        echo 'UserId: ' . $userId . ', Message: ' . $message;
    }

    public function actionTest()
    {
        echo \Yii::$app->redis->hostname;
        //$client = new \swoole_redis;
//        $client->on('message', static function (\Swoole\Redis $client, $result) {
//            // process data, broadcast to websocket clients
//            if ($result[0] == 'message') {
//                echo ' -- ' . $result[1] . PHP_EOL;
//                /*foreach($server->connections as $fd) {
//                    $server->push($fd, $result[1]);
//                    echo ' -- ' . $result[1] . PHP_EOL;
//                }*/
//            }
//        });
//        $client->connect('localhost', 6379, static function (\Swoole\Redis $client, $result) {
//            $client->subscribe('user-167');
//        });
        //echo 123;
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


//            \yii\helpers\VarDumper::dump($cookieName);
//            \yii\helpers\VarDumper::dump($dataCookie);

        if ($dataCookie) {
            $data = @unserialize($dataCookie, ['allowed_classes' => false]);
            //\yii\helpers\VarDumper::dump($data);
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
                            //VarDumper::dump(['identity' => $identity, 'duration' => $duration]);
                            //return ['identity' => $identity, 'duration' => $duration];
                        }
                    }
                }
            }
        }

        return null;
    }


}