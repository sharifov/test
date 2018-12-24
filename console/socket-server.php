<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 12/20/18
 * Time: 5:27 PM
 */


require_once __DIR__ . '/../vendor/autoload.php';
use Workerman\Worker;

/** @var \Workerman\Connection\TcpConnection[] $user */
$user = [];

/** @var \Workerman\Connection\TcpConnection[][] $userConnections */
$userConnections = [];

/** @var \Workerman\Connection\TcpConnection[] $leadConnections */
$leadConnections = [];

$connectionsUser = [];
$connectionsLead = [];

$ws_worker = new Worker('websocket://localhost:8080');
$ws_worker->name = 'WebsocketWorker';
//$ws_worker->user = 'www-data';


$ws_worker->onWorkerStart = function() use (&$user, &$userConnections, &$leadConnections)
{

    $inner_tcp_worker = new Worker('tcp://127.0.0.1:1234');
    $inner_tcp_worker->name = 'TcpWorker';
    //$inner_tcp_worker->user = 'www-data';

    $inner_tcp_worker->onMessage = function(\Workerman\Connection\TcpConnection $connection, $data) use (&$user, &$userConnections, &$leadConnections)
    {

        //$connection->send('Hello '.$data);
        $data = @json_decode($data);

        // Send message - userId
        if ($data) {
            if(isset($data->user_id) && $data->user_id) {

                if ($data->multiple) {
                    if (isset($userConnections[$data->user_id]) && is_array($userConnections[$data->user_id])) {
                        foreach ($userConnections[$data->user_id] as $wc) {
                            $wc->send(json_encode($data->data));
                            echo '>> send multiple messages to con: "' . $wc->id . '", user: ' . $data->user_id . "\r\n";
                        }
                    }

                } else {

                    if (isset($user[$data->user_id]) && $wc = $user[$data->user_id]) {
                        $wc->send(json_encode($data->data));
                        echo '> send one message to con: "' . $wc->id . '", user: ' . $data->user_id . "\r\n";
                    }
                }
            }

            if(isset($data->lead_id)) {
                if (isset($leadConnections[$data->lead_id]) && is_array($leadConnections[$data->lead_id])) {
                    foreach ($leadConnections[$data->lead_id] as $wc) {
                        $wc->send(json_encode($data->data));
                        echo '>> send multiple message to con: "' . $wc->id . '", lead_id: ' . $data->lead_id . "\r\n";
                    }
                }
            }


        }
    };
    $inner_tcp_worker->listen();
};

$ws_worker->onConnect = function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead)
{
    $connection->onWebSocketConnect = function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead)
    {
        $user_id = isset($_GET['user_id']) && $_GET['user_id'] > 0 ? (int) $_GET['user_id'] : null;
        $lead_id = isset($_GET['lead_id']) && $_GET['lead_id'] > 0 ? (int) $_GET['lead_id'] : null;

        if($user_id) {
            $user[$user_id] = $connection;
            $userConnections[$user_id][$connection->id] = $connection;
            $connectionsUser[$connection->id] = $user_id;
        }

        if($lead_id) {
            $leadConnections[$lead_id][$connection->id] = $connection;
            $connectionsLead[$connection->id] = $lead_id;
        }

        $connection->send('connection_id: ' . $connection->id );
        echo '+ connect "'.$connection->id.'" ';
        if($user_id) {
            echo 'user_id: '.$user_id.' ';
        }

        if($lead_id) {
            echo 'lead_id: '.$lead_id.' ';
        }
        echo "\r\n";
        //var_dump($connection->id);

        // $_COOKIE['PHPSESSID']
    };
};

$ws_worker->onClose = function(\Workerman\Connection\TcpConnection $connection) use(&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead)
{
    /*$user_id = array_search($connection, $user);
    if($user_id) {
        unset($user[$user_id]);
        echo 'Remove connection "'.$connection->id.'", user_id: ' . $user_id . "\r\n";
    }*/


    if(isset($connectionsUser[$connection->id]) && $connectionsUser[$connection->id]) {

        $user_id = $connectionsUser[$connection->id];
        unset($connectionsUser[$connection->id]);
        if(isset($userConnections[$user_id][$connection->id])) {
            unset($userConnections[$user_id][$connection->id]);
            if(isset($userConnections[$user_id]) && count($userConnections[$user_id]) > 0) {

            } else {
                if(isset($user[$user_id])) {
                    unset($user[$user_id]);
                }
            }
        }

        echo '- disconnect "'.$connection->id.'"  user: ' . $user_id . "\r\n";
    }

    if(isset($connectionsLead[$connection->id]) && $connectionsLead[$connection->id]) {

        $lead_id = $connectionsLead[$connection->id];
        unset($connectionsLead[$connection->id]);

        if(isset($leadConnections[$lead_id][$connection->id])) {
            unset($leadConnections[$lead_id][$connection->id]);
        }

        echo '- disconnect "'.$connection->id.'" lead: ' . $lead_id . "\r\n";

    }


    $connection->send('connection_id: ' . $connection->id);
};



$ws_worker->onMessage = function(\Workerman\Connection\TcpConnection $connection, $data) use (&$connectionsUser, &$connectionsLead)
{
    $dataObj = @json_decode($data);

    if($dataObj) {
        if(isset($dataObj->act)) {
            switch ($dataObj->act) {
                case 'getUserConnections': $connection->send('Count of user connections: ' . count($connectionsUser));
                    break;
                case 'getLeadConnections': $connection->send('Count of lead connections: ' . count($connectionsLead));
                    break;
            }
        }
    }
    echo 'Get message ' . "\r\n";

    $connection->send('Hello '.$data);

};


// Run worker
Worker::runAll();