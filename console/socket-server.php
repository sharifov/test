<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 12/20/18
 * Time: 5:27 PM
 */



require_once __DIR__ . '/../vendor/autoload.php';


$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../common/config/main.php',
    require __DIR__ . '/../common/config/main-local.php'
    //require __DIR__ . '/../console/config/main.php',
    //require __DIR__ . '/../console/config/main-local.php'
);


echo 'Current DT: ' . date('Y-m-d H:i:s')."\r\n";

date_default_timezone_set('UTC');
//date_timezone_set('UTC');

echo 'Current UTC: ' . date('Y-m-d H:i:s')."\r\n";


try {
    $db = new PDO($config['components']['db']['dsn'], $config['components']['db']['username'], $config['components']['db']['password']);
    $db->exec('DELETE FROM user_connection');
} catch (PDOException $e) {
    print 'Error!: ' . $e->getMessage() . "\r\n";
    die();
}


use Workerman\Worker;

/** @var \Workerman\Connection\TcpConnection[] $user */
$user = [];

/** @var \Workerman\Connection\TcpConnection[][] $userConnections */
$userConnections = [];

/** @var \Workerman\Connection\TcpConnection[] $leadConnections */
$leadConnections = [];

$connectionsUser = [];
$connectionsLead = [];

$ws_worker = new Worker('websocket://0.0.0.0:8080');
$ws_worker->name = 'WebsocketWorker';
//$ws_worker->user = 'www-data';


$ws_worker->onWorkerStart = function() use (&$user, &$userConnections, &$leadConnections)
{

    $inner_tcp_worker = new Worker('tcp://0.0.0.0:1234');
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

$ws_worker->onConnect = function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead, &$db)
{
    $connection->onWebSocketConnect = function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead, &$db)
    {

        date_default_timezone_set('UTC');

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


        echo '+ connect "'.$connection->id.'", ';
        if($user_id) {
            echo 'user_id: "'.$user_id.'", ';
            /*$user = \common\models\Employee::findOne($user_id);
            if($user) {
                echo 'username: '.$user->username.', ';
            }*/
        }

        if($lead_id) {
            echo 'lead_id: "'.$lead_id.'", ';
        }



        echo 'ip: '.$connection->getRemoteIp().', ';
        echo 'useragent: '.$_SERVER['HTTP_USER_AGENT'].', ';
        echo 'dt: ' . date('Y-m-d H:i:s');


        $data = [
            'uc_connection_id'          => $connection->id,
            'uc_user_id'                => $user_id,
            'uc_lead_id'                => $lead_id,
            'uc_user_agent'             => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'uc_controller_id'          => $_GET['controller_id'] ?? null,
            'uc_action_id'              => $_GET['action_id'] ?? null,
            'uc_page_url'               => $_GET['page_url'] ?? null,
            'uc_ip'                     => $connection->getRemoteIp(),
            'uc_created_dt'             => date('Y-m-d H:i:s'),
        ];

        $sql = 'INSERT INTO user_connection (uc_connection_id, uc_user_id, uc_lead_id, uc_user_agent, uc_controller_id, uc_action_id, uc_page_url, uc_ip, uc_created_dt) 
                VALUES (:uc_connection_id, :uc_user_id, :uc_lead_id, :uc_user_agent, :uc_controller_id, :uc_action_id, :uc_page_url, :uc_ip, :uc_created_dt)';


        try {
            //$db->exec('DELETE FROM user_connection');

            $stmt= $db->prepare($sql);
            $stmt->execute($data);

            /*foreach($dbh->query('SELECT * from employees') as $row) {
                print_r($row);
            }*/
            //$db = null;
        } catch (PDOException $e) {
            print 'Error!: ' . $e->getMessage() . "\r\n";
        }


        $json = json_encode(['connection_id' => $connection->id, 'command' => 'initConnection']);

        $connection->send($json);

        echo "\r\n";
        //var_dump($connection->id);

        // $_COOKIE['PHPSESSID']
    };
};

$ws_worker->onClose = function(\Workerman\Connection\TcpConnection $connection) use(&$user, &$userConnections, &$leadConnections, &$connectionsUser, &$connectionsLead, &$db)
{
    /*$user_id = array_search($connection, $user);
    if($user_id) {
        unset($user[$user_id]);
        echo 'Remove connection "'.$connection->id.'", user_id: ' . $user_id . "\r\n";
    }*/


    try {

        $sql = 'DELETE FROM user_connection WHERE uc_connection_id = :connectionId';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':connectionId', $connection->id, PDO::PARAM_INT);
        $stmt->execute();

        //$db->exec('DELETE FROM user_connection WHERE ');

        //$stmt= $db->prepare($sql);
        //$stmt->execute($data);

        /*foreach($dbh->query('SELECT * from employees') as $row) {
            print_r($row);
        }*/
        //$db = null;
    } catch (PDOException $e) {
        print 'Error!: ' . $e->getMessage() . "\r\n";
    }


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

    $json = json_encode(['connection_id' => $connection->id, 'command' => 'closeConnection']);

    $connection->send($json);
};



$ws_worker->onMessage = function(\Workerman\Connection\TcpConnection $connection, $data) use (&$connectionsUser, &$connectionsLead)
{
    $dataObj = @json_decode($data);

    $dataResponse = [];

    if($dataObj) {
        if(isset($dataObj->act)) {
            switch ($dataObj->act) {
                case 'getUserConnections': $dataResponse = ['command' => 'countUserConnection', 'cnt' => count($connectionsUser)];
                    break;
                case 'getLeadConnections': $dataResponse = ['command' => 'countLeadConnection', 'cnt' => count($connectionsLead)];
                    break;
            }
        }
    }
    echo 'Get message ' . "\r\n";

    $connection->send(json_encode($dataResponse));

};


// Run worker
Worker::runAll();