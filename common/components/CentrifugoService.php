<?php


namespace common\components;

use phpcent\Client;

class CentrifugoService
{
    public static function sentMsg(){
        $client = new Client("https://localhost:8000/api");

        $client->setSafety(false);

        $client->setApiKey("620b23a5-1885-4755-9908-527360b8bc8a");
        $client->publish("news", ["message" => "Hello Centrifugo World !!!"]);
    }
}