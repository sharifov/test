<?php
/**
 * User: alex.connor
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
ini_set('mysql.connect_timeout',0);
//date_timezone_set('UTC');

echo 'Current UTC: ' . date('Y-m-d H:i:s')."\r\n";


try {
    //$options = [PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $db = new PDO($config['components']['db']['dsn'], $config['components']['db']['username'], $config['components']['db']['password']); // , $options
    $db->exec('DELETE FROM user_connection WHERE uc_created_dt <= "' . date('Y-m-d H:i:s', strtotime('-10 minutes')) . '"');
    $db->exec('DELETE FROM user_online WHERE uo_user_id NOT IN (SELECT DISTINCT uc_user_id FROM user_connection)');
} catch (PDOException $e) {
    print 'Error!: ' . $e->getMessage() . "\r\n";
    die();
}


use Workerman\Worker;

/** @var \Workerman\Connection\TcpConnection[] $user */
$user = [];

/** @var \Workerman\Connection\TcpConnection[][] $userConnections */
$userConnections = [];

$connectionsUser = [];

$worker = new Worker('websocket://127.0.0.1:8080');
$worker->name = 'WebsocketWorker';
$worker->user = 'www-data';

$worker::$pidFile = __DIR__ . '/../console/runtime/worker.pid';


$worker->onWorkerStart = static function() use (&$user, &$userConnections)
{

    $inner_tcp_worker = new Worker('tcp://127.0.0.1:1234');
    $inner_tcp_worker->name = 'TcpWorker';
    //$inner_tcp_worker->user = 'www-data';

    $inner_tcp_worker->onMessage = static function(\Workerman\Connection\TcpConnection $connection, $data) use (&$userConnections)
    {

        //$connection->send('Hello '.$data);
        $data = @json_decode($data);

        // Send message
        if ($data) {
            if(!empty($data->user_id)) {



                $sqlQuery = 'SELECT DISTINCT connection_id FROM user_connection WHERE uc_user_id = :user_id';

                $sqlData = [
                    'user_id'    => $data->user_id,
                ];

                $stmt= $db->prepare($sqlQuery);
                $stmt->execute($sqlData);
                $connectionList = $stmt->fetchAll();

                if (!empty($connectionList)) {
                    foreach ($connectionList as $item) {
                        if (!empty($item['uc_connection'])) {
                            $wc = unserialize($item['uc_connection']);
                            $wc->send(json_encode($data->data));
                            echo '> send one message to con: "' . $wc->id . '", user: ' . $data->user_id . "\r\n";
                        }
                    }
                }

//                if ($data->multiple) {
//                    if (isset($userConnections[$data->user_id]) && is_array($userConnections[$data->user_id])) {
//                        foreach ($userConnections[$data->user_id] as $wc) {
//                            $wc->send(json_encode($data->data));
//                            echo '>> send multiple messages to con: "' . $wc->id . '", user: ' . $data->user_id . "\r\n";
//                        }
//                    }
//
//                } else {
//
//                    if (isset($user[$data->user_id]) && $wc = $user[$data->user_id]) {
//                        $wc->send(json_encode($data->data));
//                        echo '> send one message to con: "' . $wc->id . '", user: ' . $data->user_id . "\r\n";
//                    }
//                }
            }

//            if(isset($data->lead_id)) {
//                if (isset($leadConnections[$data->lead_id]) && is_array($leadConnections[$data->lead_id])) {
//                    foreach ($leadConnections[$data->lead_id] as $wc) {
//                        $wc->send(json_encode($data->data));
//                        echo '>> send multiple message to con: "' . $wc->id . '", lead_id: ' . $data->lead_id . "\r\n";
//                    }
//                }
//            }
//
//            if(isset($data->case_id)) {
//                if (isset($caseConnections[$data->case_id]) && is_array($caseConnections[$data->case_id])) {
//                    foreach ($caseConnections[$data->case_id] as $wc) {
//                        $wc->send(json_encode($data->data));
//                        echo '>> send multiple message to con: "' . $wc->id . '", case_id: ' . $data->case_id . "\r\n";
//                    }
//                }
//            }


        }
    };
    $inner_tcp_worker->listen();
};

$worker->onConnect = static function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$connectionsUser, &$db, &$config)
{
    $connection->onWebSocketConnect = static function(\Workerman\Connection\TcpConnection $connection) use (&$user, &$userConnections, &$connectionsUser, &$db, &$config)
    {

        date_default_timezone_set('UTC');

        $user_id = isset($_GET['user_id']) && $_GET['user_id'] > 0 ? (int) $_GET['user_id'] : null;
        $lead_id = isset($_GET['lead_id']) && $_GET['lead_id'] > 0 ? (int) $_GET['lead_id'] : null;
        $case_id = isset($_GET['case_id']) && $_GET['case_id'] > 0 ? (int) $_GET['case_id'] : null;

//        if($user_id) {
//            $user[$user_id] = $connection;
//            $userConnections[$user_id][$connection->id] = $connection;
//            $connectionsUser[$connection->id] = $user_id;
//        }

//        if($lead_id) {
//            $leadConnections[$lead_id][$connection->id] = $connection;
//            $connectionsLead[$connection->id] = $lead_id;
//        }
//
//        if($case_id) {
//            $caseConnections[$case_id][$connection->id] = $connection;
//            $connectionsCase[$connection->id] = $case_id;
//        }


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

        if($case_id) {
            echo 'case_id: "'.$case_id.'", ';
        }


        $ip = $_GET['ip'] ?? null;
        $ua = $_GET['ua'] ?? $_SERVER['HTTP_USER_AGENT'];

        echo 'ip: '.$ip.', ';
        echo 'useragent: '.$ua.', ';
        echo 'dt: ' . date('Y-m-d H:i:s');




        try {

            $onlineData = [
                'uo_user_id'    => $user_id,
            ];

            $sqlOnline = 'SELECT uo_user_id FROM user_online WHERE uo_user_id = :uo_user_id';

            $stmt= $db->prepare($sqlOnline);
            $stmt->execute($onlineData);

            $uaRow = $stmt->fetch();
            if (!$uaRow || !isset($uaRow['uo_user_id'])) {

                //echo 'Not exist userOnline: '.$user_id.' ';

                $onlineData['uo_updated_dt'] = date('Y-m-d H:i:s');
                $sqlOnline = 'INSERT INTO user_online (uo_user_id, uo_updated_dt) VALUES (:uo_user_id, :uo_updated_dt)';
                $stmt= $db->prepare($sqlOnline);
                $stmt->execute($onlineData);
            }
//            else {
//                //echo 'UserOnline exist '.$user_id.' ';
//            }

            //$res = $db->query($sqlOnline);

            //$uc_id = $db->lastInsertId();

            /*foreach($dbh->query('SELECT * from employees') as $row) {
                print_r($row);
            }*/
            //$db = null;
        } catch (PDOException $e) {
            print 'Error!: ' . $e->getMessage() . "\r\n";
        }


        $data = [
            'uc_connection_id'          => $connection->id,
            'uc_user_id'                => $user_id,
            'uc_lead_id'                => $lead_id,
            'uc_user_agent'             => $ua,
            'uc_controller_id'          => $_GET['controller_id'] ?? null,
            'uc_action_id'              => $_GET['action_id'] ?? null,
            'uc_page_url'               => $_GET['page_url'] ?? null,
            'uc_ip'                     => $ip, //$connection->getRemoteIp(),
            'uc_created_dt'             => date('Y-m-d H:i:s'),
            'uc_case_id'                => $case_id,
            'uc_connection'             => serialize($connection)
        ];

        $sql = 'INSERT INTO user_connection (uc_connection_id, uc_user_id, uc_lead_id, uc_user_agent, uc_controller_id, uc_action_id, uc_page_url, uc_ip, uc_created_dt, uc_case_id, uc_connection) 
                VALUES (:uc_connection_id, :uc_user_id, :uc_lead_id, :uc_user_agent, :uc_controller_id, :uc_action_id, :uc_page_url, :uc_ip, :uc_created_dt, :uc_case_id, :uc_connection)';

        $uc_id = 0;

        try {
            //$db->exec('DELETE FROM user_connection');

            $stmt= $db->prepare($sql);
            $stmt->execute($data);

            $uc_id = $db->lastInsertId();

            /*foreach($dbh->query('SELECT * from employees') as $row) {
                print_r($row);
            }*/
            //$db = null;
        } catch (PDOException $e) {
            print 'Error!: ' . $e->getMessage() . "\r\n";
        }

        if(!$uc_id) {
            $db = new PDO($config['components']['db']['dsn'], $config['components']['db']['username'], $config['components']['db']['password']);
            $stmt= $db->prepare($sql);
            $stmt->execute($data);
            $uc_id = $db->lastInsertId();
        }

        $json = json_encode(['connection_id' => $connection->id, 'command' => 'initConnection', 'uc_id' => $uc_id]);

        $connection->send($json);

        echo "\r\n";
        //var_dump($connection->id);

        // $_COOKIE['PHPSESSID']
    };
};

$worker->onClose = static function(\Workerman\Connection\TcpConnection $connection) use(&$user, &$userConnections, &$connectionsUser, &$db)
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


//    if(isset($connectionsUser[$connection->id]) && $connectionsUser[$connection->id]) {
//
//        $user_id = $connectionsUser[$connection->id];
//        unset($connectionsUser[$connection->id]);
//        if(isset($userConnections[$user_id][$connection->id])) {
//            unset($userConnections[$user_id][$connection->id]);
//            if(isset($userConnections[$user_id]) && count($userConnections[$user_id]) > 0) {
//
//                foreach ($userConnections[$user_id] as $connectionId) {
//                    $user[$user_id] = $connectionId;
//                    //break;
//                }
//
//            } else {
//                if(isset($user[$user_id])) {
//                    unset($user[$user_id]);
//                }
//            }
//        }
//
//        if (!isset($user[$user_id]) || !$user[$user_id]) {
//            try {
//                $sql = 'DELETE FROM user_online WHERE uo_user_id = :user_id';
//                $stmt = $db->prepare($sql);
//                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
//                $stmt->execute();
//            } catch (PDOException $e) {
//                print 'Error!: ' . $e->getMessage() . "\r\n";
//            }
//            // echo "not exist uc: " . $user_id . "\r\n";
//        }
//
//
//
//        echo '- disconnect "'.$connection->id.'"  user: ' . $user_id . "\r\n";
//    }

    $db->exec('DELETE FROM user_online WHERE uo_user_id NOT IN (SELECT DISTINCT uc_user_id FROM user_connection)');

    $json = json_encode(['connection_id' => $connection->id, 'command' => 'closeConnection']);

    $connection->send($json);
};



$worker->onMessage = static function(\Workerman\Connection\TcpConnection $connection, $data) use (&$connectionsUser)
{
    $dataObj = @json_decode($data);

    $dataResponse = [];

    if($dataObj) {
        if(isset($dataObj->act)) {
            switch ($dataObj->act) {
                case 'getUserConnections': $dataResponse = ['command' => 'countUserConnection', 'cnt' => count($connectionsUser)];
                    break;
//                case 'getLeadConnections': $dataResponse = ['command' => 'countLeadConnection', 'cnt' => count($connectionsLead)];
//                    break;
//                case 'getCaseConnections': $dataResponse = ['command' => 'countCaseConnection', 'cnt' => count($connectionsCase)];
//                    break;
            }
        }
    }
    echo 'Get message ' . "\r\n";

    $connection->send(json_encode($dataResponse));

};


// Run worker
Worker::runAll();