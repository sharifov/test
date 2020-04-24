<?php
namespace console\controllers;

use common\models\UserConnection;
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

        $server->on('start', static function (Server $server) {
            echo ' Swoole WebSocket Server is started at ' . $server->host.':'.$server->port . PHP_EOL;
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

        $server->on('workerStart', static function ($server, $workerId) {
            echo ' Worker (Id: ' . $workerId . ')  start: ' . date('Y-m-d H:i:s') . PHP_EOL;
        });

        $server->on('open', static function(Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass, $redisConfig) {

            echo '+ ' . date('m-d H:i:s'). " op: {$request->fd}";
            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {

                $userId = $user->getId();

                $server->push($request->fd, json_encode(['cmd' => 'userInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING

                $server->tick(30000, static function() use ($server, $request) {
                    $server->push($request->fd, json_encode(['cmd' => 'pong', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                });

                //VarDumper::dump($request);
                //VarDumper::dump($request->get);

                $ua = !empty($request->header['user-agent']) ? substr($request->header['user-agent'], 0, 255) : null;
                $ip = !empty($request->server['remote_addr']) ? substr($request->server['remote_addr'], 0, 40) : null;
                $subList = empty($request->get['sub_list']) || !is_array($request->get['sub_list']) ? [] : $request->get['sub_list'];

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
                $userConnection->uc_app_instance = \Yii::$app->params['appInstance'] ?? null;
                $userConnection->uc_sub_list = $subList ? @json_encode($subList) : null;

                if (!$userConnection->save()) {
                    \Yii::error(VarDumper::dumpAsString($userConnection->errors), 'WS:UserConnection:save');
                }

//                foreach($server->tblConnections as $row)
//                {
//                    VarDumper::dump($row);
//                }

//                VarDumper::dump(['fd' => $request->fd,
//                    'uid' => $uid,
//                    'user_id' => $userId,
//                    'name' => $user->username,
//                    'dt' => date('Y-m-d H:i:s')]);

                echo ' : ' . $user->username . ' ('.$userId.')' . PHP_EOL;

                unset($user);


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


                $json = json_encode(['cmd' => 'initConnection', 'connection_id' => $userConnection->uc_connection_id, 'uc_id' => $userConnection->uc_id]);
                $server->push($request->fd, $json); //WEBSOCKET_OPCODE_PING

                $redis = new \Swoole\Coroutine\Redis();

                $redis->connect($redisConfig['host'], $redisConfig['port']);
                //$val = $redis->get('key');

                if ($subList) {
                    foreach ($subList as $k => $value) {
                        if (strpos($value, 'user-') !== false) {
                            unset($subList[$k]);
                        }
                    }
                }

                $subList[] = 'user-' . $userId;
                $subList[] = 'con-' . $userConnection->uc_id;

                $msg = $redis->subscribe($subList);

                while ($msg = $redis->recv())
                {
                    //VarDumper::dump($msg);
                    if (!empty($msg[0]) && $msg[0] === 'message') {
                        // echo 'mes: ' . $msg[2] . PHP_EOL;
                        $server->push($request->fd, $msg[2]); //WEBSOCKET_OPCODE_PING
                    }
                }

            } else {
                echo ' : not init user' . PHP_EOL;
                $server->push($request->fd, json_encode(['cmd' => 'userNotInit', 'time' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                $server->disconnect($request->fd, 403, 'Access denied');
            }

        });


        $server->on('message', static function(Server $server, \Swoole\WebSocket\Frame $frame) {
            echo "- received message: {$frame->data}\n";
            $data['connection_info'] = $server->connection_info($frame->fd);
            //$data['client_info'] = $server->getClientInfo($frame->fd);
            $data['connection_list'] = $server->connection_list();
            $data['data'] = $frame->data;
            $data['dt'] = date('Y-m-d H:i:s');
            $server->push($frame->fd, json_encode($data));
        });

        $server->on('close', static function(Server $server, int $fd) {
            echo '- ' . date('m-d H:i:s'). " cl: {$fd}\n";
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
            echo '> ' . $message . PHP_EOL;
            \Yii::error($message, 'WS:'. __METHOD__);
        });

        $server->start();
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