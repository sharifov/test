<?php
namespace console\controllers;

use common\models\Employee;
use console\daemons\ChatServer;
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
        $redis->publish('user-' . $userId, $message);
        echo 'UserId: ' . $userId . ', Message: ' . $message;
    }


    public function actionStart2()
    {
        $thisClass = $this;
        $frontendConfig = ArrayHelper::merge(
            require \Yii::getAlias('@frontend/config/main.php'),
            require \Yii::getAlias('@frontend/config/main-local.php')
        );

        $server = new \Swoole\Websocket\Server('localhost', 8080);
        $server->set(['worker_num' => 2, 'task_worker_num' => 2,]);

        $server->on('start', static function (\Swoole\WebSocket\Server $server) {
            echo '- Swoole WebSocket Server is started at ' . $server->host.':'.$server->port . PHP_EOL;
        });




        $server->on('pipeMessage', static function(\Swoole\WebSocket\Server $server, $src_worker_id, $data) {
            echo "#{$server->worker_id} message from #$src_worker_id: $data\n";
        });

        $server->on('open', static function(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) use ($frontendConfig, $thisClass) {

            echo "- connection open: {$request->fd}\n";

            $user = $thisClass->getIdentityByCookie($request, $frontendConfig);

            if ($user) {
                $server->push($request->fd, json_encode(['userInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING

                \Yii::$app->redis->subscribe('user-' . $user->getId());

            } else {
                echo '- not init user' . PHP_EOL;
                $server->push($request->fd, json_encode(['userNotInit', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
            }

            //\yii\helpers\VarDumper::dump($session);

            //$cookieValue = \Yii::$app->getRequest()->getCookies();//->getValue('_identity-crm');

            //$user = Employee::find()->orderBy('id DESC')->limit(1)->one();
            //\yii\helpers\VarDumper::dump($frontendConfig['components']['user']['identityCookie']['name']);

            $server->tick(5000, static function() use ($server, $request) {
                $server->push($request->fd, json_encode(['pong', date('H:i:s')])); //WEBSOCKET_OPCODE_PING
            });
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