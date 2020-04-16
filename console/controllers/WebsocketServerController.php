<?php
namespace console\controllers;

use common\models\Employee;
use console\daemons\ChatServer;
use hollisho\redis_pub_sub\RedisPubSub;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * Class WebsocketServerController
 * @package console\controllers
 */
class WebsocketServerController extends Controller
{
    public function actionStart($port = null)
    {
        $server = new ChatServer();
        if ($port) {
            $server->port = $port;
        }
        $server->start();
        //$user = \Yii::$app->user;
        //VarDumper::dump($user->id);
    }


    /**
     * @param \Swoole\Http\Request $request
     * @param array $frontendConfig
     * @return IdentityInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    private function getIdentityByCookie(\Swoole\Http\Request $request, array $frontendConfig): ?IdentityInterface
    {
        //$cookieName = $frontendConfig['components']['user']['identityCookie']['name'] ?? '';

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

    public function actionPublish($userId, $message = 'Hello')
    {
        /** @var \yii\redis\Connection $redis */
        $redis = \Yii::$app->redis;
        for ($i =1; $i <= 10; $i++) {
            $redis->publish('user-' . $userId, $message . $i);
        }
        echo 'UserId: ' . $userId . ', Message: ' . $message;
    }

    public function actionStart3()
    {
       $client = new \swoole_redis;
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
        echo 123;
    }


    public function actionStart2()
    {
        $thisClass = $this;
        $frontendConfig = ArrayHelper::merge(
            require \Yii::getAlias('@frontend/config/main.php'),
            require \Yii::getAlias('@frontend/config/main-local.php')
        );



//        $redis2 = new \Swoole\Coroutine\Redis();
//        $redis2->connect("localhost", 6379);


        /* @var $redisPubSub RedisPubSub */
        //$redisPubSub = \Yii::$app->redisPubSub;
        //$redisPubSub->setOptReadTimeout(-1);

        $wsConfig = \Yii::$app->params['webSocketServer'];
        $wsHost = $wsConfig['host'] ?: 'localhost';
        $wsPort = $wsConfig['port'] ?: 8080;

        $server = new \Swoole\Websocket\Server($wsHost, $wsPort);

        if (!empty($wsConfig['settings'])) {
            $server->set($wsConfig['settings']); //, 'task_worker_num' => 1]);
        }

        $server->on('start', static function (\Swoole\WebSocket\Server $server) {
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

        /** @var \yii\redis\Connection $redis */
        //$redis = \Yii::$app->redis;

        $server->on('open', static function(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass) {

            echo "- connection open: {$request->fd}\n";

            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {
                $server->push($request->fd, json_encode(['userInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
            } else {
                echo '- not init user' . PHP_EOL;
                $server->push($request->fd, json_encode(['userNotInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                $server->disconnect($request->fd, 403, 'Access denied');
            }

            //\yii\helpers\VarDumper::dump($session);

            //$cookieValue = \Yii::$app->getRequest()->getCookies();//->getValue('_identity-crm');

            //$user = Employee::find()->orderBy('id DESC')->limit(1)->one();
            //\yii\helpers\VarDumper::dump($frontendConfig['components']['user']['identityCookie']['name']);

            $server->tick(10000, static function() use ($server, $request) {
                $server->push($request->fd, json_encode(['pong', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
            });


            if ($user) {

                VarDumper::dump($request);

                $userId = $user->getId();
                $redisCor = new \Swoole\Coroutine\Redis();
                $redisCor->connect('127.0.0.1', 6379);
                //$val = $redis->get('key');

                $msg = $redisCor->subscribe(['user-' . $userId]);

                //VarDumper::dump($msg);

                while ($msg = $redisCor->recv())
                {
                    VarDumper::dump($msg);
                    if (!empty($msg[0]) && $msg[0] === 'message') {
                       echo $msg[2] . PHP_EOL;

                        $server->push($request->fd, json_encode(['message' => $msg[2], 't' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
                    }
                }

//                $msg = $redis2->subscribe(['user-' . $userId]);
//                while ($msg = $redis2->recv())
//                {
//                    VarDumper::dump($msg);
//                }

//                $server->tick(2000, static function() use ($server, $request, $redis, $userId) {
//                    $msg = $redis->subscribe('user-' . $userId);
//                   // VarDumper::dump($msg);
//                    if (!empty($msg[0]) && $msg[0] === 'message') {
//                       echo $msg[2] . PHP_EOL;
//
//                        $server->push($request->fd, json_encode(['message' => $msg[2], 't' => date('H:i:s')])); //WEBSOCKET_OPCODE_PING
//                    }
//                });

//                while(1) {
//                    $msg = $redis->subscribe('user-' . $user->getId());
//                    if (!empty($msg[0]) && $msg[0] == 'message') {
//                        echo $msg[0] . PHP_EOL;
//                    }
//                }
                //$subscription
                //VarDumper::dump($msg);

//                $redis->on('message', function ($e) {
//                    VarDumper::dump($e);
//                });

                /*\Yii::$app->redisPubSub->subscribe('user-' . $user->getId(), static function($instance, $channelName, $message) {
                    var_dump($message);
                });*/

                //$redis = new \Redis();


//                $redisPubSub->subscribe('user-' . $user->getId(), function($instance, $channelName, $message) {
//                    VarDumper::dump($instance);
//                    VarDumper::dump($channelName);
//                    VarDumper::dump($message);
//                });


//                $redis->subscribe('user-' . $user->getId(), static function($instance, $channelName, $message) {
//                    var_dump($message);
//                });

//                while ($msg) {
//                    VarDumper::dump($msg);
//                }
            }

        });


//$server->on('request', static function(Swoole\Http\Request $request, Swoole\Http\Response $response) {
//    echo "connection open: {$request->fd}\n";
//    $response->end("<h1>hello swoole</h1>");
//});

//public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
//{
//    Yii::$app->request->setRequest($request);
//    Yii::$app->response->setResponse($response);
//    Yii::$app->run();
//    Yii::$app->response->clear();
//}

        $server->on('message', static function(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {
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

        $server->on('close', static function(\Swoole\WebSocket\Server $server, int $fd) {
            echo "- connection close: {$fd}\n";
        });

        $server->start();
    }




//    public function actionStart()
//    {
//        $server = new WebSocketServer();
//        $server->port = 8080; //This port must be busy by WebServer and we handle an error
//
//        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, static function($e) use($server) {
//            //echo "Error opening port " . $server->port . "\n";
//            echo '- Error Open WebSocketServer: ' . $e->exception->getMessage() . "\n";
//            echo '- Init Server port: ' . $server->port . "\n";
//            //$server->port += 1; //Try next port to open
//            //$server->start();
//        });
//
//        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, static function($e) use($server) {
//            echo '- Server started at port: ' . $server->port;
//        });
//
//        $server->start();
//    }



}