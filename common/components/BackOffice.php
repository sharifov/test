<?php
namespace common\components;

use Yii;

class BackOffice
{
    public static function sendRequest($endpoint, $type = 'GET', $fields = null)
    {
        $url = sprintf('%s/%s', Yii::$app->params['sync']['serverUrl'], $endpoint);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_POST, true);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'version: ' . Yii::$app->params['sync']['ver'],
            'signature: ' . self::getSignature()
        ]);
        $result = curl_exec($ch);

        /*Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s\n\nResponse:\n%s",
            print_r($fields, true),
            print_r(curl_getinfo($ch), true),
            print_r($result, true)
        ), 'BackOffice component');*/

        return json_decode($result, true);
    }

    private function getSignature(): string
    {
        $expired = time() + 3600;
        $md5 = md5(sprintf('%s:%s:%s', Yii::$app->params['sync']['apiKey'], Yii::$app->params['sync']['ver'], $expired));
        return implode('.', [md5($md5), $expired, $md5]);
    }
}