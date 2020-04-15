<?php
/**
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


echo 'Current  DT: ' . date('Y-m-d H:i:s')."\r\n";

date_default_timezone_set('UTC');
ini_set('mysql.connect_timeout',0);
//date_timezone_set('UTC');

echo 'Current UTC: ' . date('Y-m-d H:i:s')."\r\n";


try {
    //$options = [PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $db = new PDO($config['components']['db']['dsn'], $config['components']['db']['username'], $config['components']['db']['password']); // , $options
//    $db->exec('DELETE FROM user_connection');
//    $db->exec('DELETE FROM user_online');
} catch (PDOException $e) {
    print 'Error!: ' . $e->getMessage() . "\r\n";
    die();
}
$server = new Swoole\Websocket\Server('localhost', 8080);

$server->on('start', static function (Swoole\WebSocket\Server $server) {
    echo '- Swoole WebSocket Server is started at ' . $server->host.':'.$server->port . PHP_EOL;
});

$server->on('open', static function(Swoole\WebSocket\Server $server, Swoole\Http\Request $request) {
    echo "connection open: {$request->fd}\n";

    \yii\helpers\VarDumper::dump($request);

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

$server->on('message', static function(Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame) {
    echo "received message: {$frame->data}\n";

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

$server->on('close', static function(Swoole\WebSocket\Server $server, int $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();
