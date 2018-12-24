<?php
namespace console\controllers;

use Workerman\Worker;

class SocketController extends  \yii\console\Controller
{

    public function actionStartSocket()
    {


        // #### create socket and listen 1234 port ####
                $tcp_worker = new Worker("tcp://0.0.0.0:8080");

        // 4 processes
                $tcp_worker->count = 4;

        // Emitted when new connection come
                $tcp_worker->onConnect = function($connection)
                {
                    echo "New Connection\n";
                };

        // Emitted when data received
                $tcp_worker->onMessage = function($connection, $data)
                {
                    // send data to client
                    $connection->send("hello $data \n");
                };

        // Emitted when new connection come
                $tcp_worker->onClose = function($connection)
                {
                    echo "Connection closed\n";
                };

        Worker::runAll();
    }
}