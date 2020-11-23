<?php
namespace common\components;

use http\Client\Request;
use http\Client\Response;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\CurlTransport;

class BackOffice
{
    public static function sendRequest($endpoint, $type = 'GET', $fields = null)
    {
        $url = sprintf('%s/%s', Yii::$app->params['backOffice']['serverUrl'], $endpoint);
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
            'version: ' . Yii::$app->params['backOffice']['ver'],
            'signature: ' . self::getSignatureBO(Yii::$app->params['backOffice']['apiKey'], Yii::$app->params['backOffice']['ver'])
        ]);
        $result = curl_exec($ch);

        /*Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s\n\nResponse:\n%s",
            print_r($fields, true),
            print_r(curl_getinfo($ch), true),
            print_r($result, true)
        ), 'BackOffice component');*/

        return json_decode($result, true);
    }


    /**
     * @param string $endpoint
     * @param array $fields
     * @param string $type
     * @param int $curlTimeOut
     * @param string $host
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function sendRequest2(string $endpoint = '', array $fields = [], string $type = 'POST', int $curlTimeOut = 30, string $host = '', bool $addBasicAuth = false): \yii\httpclient\Response
    {
        $host = $host ?: Yii::$app->params['backOffice']['serverUrl'];

        $uri = $host . '/' . $endpoint;
        $signature = self::getSignatureBO(Yii::$app->params['backOffice']['apiKey'], Yii::$app->params['backOffice']['ver']);

        $client = new \yii\httpclient\Client([
            'transport' => CurlTransport::class,
            'responseConfig' => [
                'format' => \yii\httpclient\Client::FORMAT_JSON
            ]
        ]);

        /*$headers = [
            //"Content-Type"      => "text/xml;charset=UTF-8",
            //"Accept"            => "gzip,deflate",
            //"Cache-Control"     => "no-cache",
            //"Pragma"            => "no-cache",
            //"Authorization"     => "Basic ".$this->api_key,
            //"Content-length"    => mb_strlen($xmlRequest),
        ];*/

        $headers = [
            'version'   => Yii::$app->params['backOffice']['ver'],
            'signature' => $signature
        ];

        if ($addBasicAuth) {
            $username = Yii::$app->params['backOffice']['username'] ?? '';
            $password = Yii::$app->params['backOffice']['password'] ?? '';
            $authStr = base64_encode($username . ':' . $password);
            $headers['Authorization'] = 'Basic ' . $authStr;
        }

        $response = $client->createRequest()
            ->setMethod($type)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setUrl($uri)
            ->addHeaders($headers)
            //->setContent($json)
            ->setData($fields)
            ->setOptions([
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => $curlTimeOut > 0 ? $curlTimeOut : 30,
            ])
            ->send();


        $metrics = new Metrics();
        if ($response->isOk) {
            $metrics->serviceCounter('back_office', ['type' => 'success', 'action' => $endpoint]);
        } else {
            $metrics->serviceCounter('back_office', ['type' => 'error', 'action' => $endpoint]);
        }

        //VarDumper::dump($response->content, 10, true); exit;
        return $response;
    }


    /**
     * @param string $apiKey
     * @param string $version
     * @return string
     */
    private static function getSignatureBO(string $apiKey = '', string $version = '') : string
    {
        $expired = time() + 3600;
        $md5 = md5(sprintf('%s:%s:%s', $apiKey, $version, $expired));
        return implode('.', [md5($md5), $expired, $md5]);
    }


    /**
     * @param array $data
     * @return mixed
     */
    public static function webHook(array $data)
    {
        $settings = \Yii::$app->params['settings'];

        $uri = Yii::$app->params['backOffice']['serverUrl'] ? Yii::$app->params['backOffice']['serverUrl'] . '/' . (Yii::$app->params['backOffice']['webHookEndpoint'] ?? '') : '';

        if (isset($settings['bo_web_hook_enable']) && $uri) {
            if ($settings['bo_web_hook_enable']) {
                try {
                    $response = self::sendRequest2($uri, $data);

                    if ($response->isOk) {
                        $result = $response->data;
                        if ($result && is_array($result)) {
                            return $result;
                        }
                    } else {
                        throw new Exception('Url: ' . $uri .' , BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
                    }
                } catch (\Throwable $exception) {
                    //throw new BadRequestHttpException($exception->getMessage());
                    \Yii::error($exception->getMessage(), 'BackOffice:webHook');
                }
            }
        } else {
            \Yii::warning('Not isset settings bo_web_hook_enable or empty params webHookEndpoint', 'UserGroupEvents:webHook');
        }
    }
}
