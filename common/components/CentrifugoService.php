<?php


namespace common\components;

use Yii;
use phpcent\Client;

class CentrifugoService
{
    private function initClient()
    {
        $client = new Client(Yii::$app->params['centrifugo']['serviceUrl']);
        $client->setApiKey(Yii::$app->params['centrifugo']['apiKey']);
        $client->setSafety(false);
        return $client;
    }

    public static function sendMsg(string $message, string $channel){
        $client = self::initClient();
        $client->publish($channel, ["message" => $message]);
    }

    /*public static function sendMsg(string $message){
        $client = new Client(Yii::$app->params['centrifugo']['serviceUrl']);

        $client->setSafety(false);

        $client->setApiKey("620b23a5-1885-4755-9908-527360b8bc8a");
        $client->publish("news", ["message" => $message]);
    }*/
}