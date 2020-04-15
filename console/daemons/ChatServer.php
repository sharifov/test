<?php


namespace console\daemons;

use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\events\WSClientMessageEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use yii\helpers\VarDumper;

class ChatServer extends WebSocketServer
{
    public function init()
    {
        parent::init();

        $server = $this;


        $this->on(self::EVENT_WEBSOCKET_OPEN_ERROR, static function($e) use($server) {
            //echo "Error opening port " . $server->port . PHP_EOL;
            echo '- Error Open WebSocketServer: ' . $e->exception->getMessage() . "\n";
            echo '- Init Server port: ' . $server->port . PHP_EOL;
            //$server->port += 1; //Try next port to open
            //$server->start();
        });

        $this->on(self::EVENT_WEBSOCKET_OPEN, static function($e) use($server) {
            echo '- Server started at port: ' . $server->port . PHP_EOL;
        });


        $this->on(self::EVENT_CLIENT_CONNECTED, static function(WSClientEvent $e) use($server) {
            //$user = \Yii::$app->user;
            $e->client->name = null;
            //echo VarDumper::dump($e->client->name);
            //$cook =\Yii::$app->getResponse()->getCookies();
            //echo ($user->isGuest ? 'Guest ' : '+ ' . VarDumper::dumpAsString($user->id)) . PHP_EOL;
            echo '+ Client Connected: ' . VarDumper::dumpAsString('') . PHP_EOL;
        });

        $this->on(self::EVENT_CLIENT_DISCONNECTED, static function(WSClientEvent $e) use($server) {
            echo '- Client Disconnected: ' . VarDumper::dumpAsString('') . PHP_EOL;
        });

        $this->on(self::EVENT_CLIENT_MESSAGE, static function(WSClientMessageEvent $e) use($server) {
            echo '- Client Message: ' . VarDumper::dumpAsString($e->message) . PHP_EOL;
        });

    }


    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @return string|null
     */
    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    public function commandChat(ConnectionInterface $client, $msg): void
    {
        $request = json_decode($msg, true);
        $result = ['message' => ''];

        if (!$client->name) {
            $result['message'] = 'Set your name';
        } elseif (!empty($request['message']) && $message = trim($request['message']) ) {

            //$user = \Yii::$app->user;
            //$m = ' + ' . ($user->isGuest ? 'g' : $user->id);

            echo '- Message: ' . $message . PHP_EOL;

            foreach ($this->clients as $chatClient) {
                $chatClient->send(json_encode([
                    'type' => 'chat',
                    'from' => $client->name,
                    'message' => $message// . $m
                ], JSON_THROW_ON_ERROR));
            }
        } else {
            $result['message'] = 'Enter message';
        }

        $client->send( json_encode($result) );
    }

    public function commandSetName(ConnectionInterface $client, $msg): void
    {
        $request = json_decode($msg, true);
        $result = ['message' => 'Username updated'];

        if (!empty($request['name']) && $name = trim($request['name'])) {
            $usernameFree = true;
            foreach ($this->clients as $chatClient) {
                if ($chatClient != $client && $chatClient->name == $name) {
                    $result['message'] = 'This name is used by other user';
                    $usernameFree = false;
                    break;
                }
            }

            if ($usernameFree) {
                $client->name = $name;
            }
        } else {
            $result['message'] = 'Invalid username';
        }

        $client->send( json_encode($result) );
    }
}