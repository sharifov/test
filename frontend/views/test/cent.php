<?php
use phpcent\Client;
$client = new \phpcent\Client("wss://localhost:8000/connection/websocket");

/*$user = bin2hex(openssl_random_pseudo_bytes(20));
$timestamp = time();*/
$token = $client->setSecret("bd08a6f0-1323-441c-9a1f-b9075e66694b")->generateConnectionToken('658',  '');

$js = <<<JS

var centrifuge = new Centrifuge('wss://localhost:8000/connection/websocket');
centrifuge.setToken('$token');

centrifuge.subscribe("news", function(message) {
    console.log(message);
});

centrifuge.connect();

JS;
$this->registerJs($js);

echo 'Test Centri Dev';
