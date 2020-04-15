<?php
namespace websocket\controllers;

use console\daemons\ChatServer;
use yii\web\Controller;
use yii\helpers\VarDumper;

/**
 * Class WebsocketServerController
 * @package console\controllers
 */
class WebsocketServerController extends Controller
{
    public function actionStart($port = null)
    {
        /*$server = new ChatServer();
        if ($port) {
            $server->port = $port;
        }



        $server->start();*/

       // $user = \Yii::$app->user;
        //VarDumper::dump($user->id);
        echo 123;
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