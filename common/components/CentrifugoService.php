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

}