<?php

namespace common\components;

use http\Client\Request;
use http\Client\Response;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
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
        $timeStart = microtime(true);
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

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('back_office', ['type' => 'success', 'action' => $endpoint]);
        } else {
            $metrics->serviceCounter('back_office', ['type' => 'error', 'action' => $endpoint]);
        }
        $seconds = round(microtime(true) - $timeStart, 1);
        $action = $action = str_replace('/', '_', $endpoint);
        $metrics->histogramMetric('back_office', $seconds, ['action' => $action]);
        unset($metrics);

        //VarDumper::dump($response->content, 10, true); exit;
        return $response;
    }


    /**
     * @param string $apiKey
     * @param string $version
     * @return string
     */
    private static function getSignatureBO(string $apiKey = '', string $version = ''): string
    {
        $expired = time() + 3600;
        $md5 = md5(sprintf('%s:%s:%s', $apiKey, $version, $expired));
        return implode('.', [md5($md5), $expired, $md5]);
    }

    public static function wh(string $type, array $data): array
    {
        if (!$type) {
            throw new \DomainException('Type is empty.');
        }

        $settings = \Yii::$app->params['settings'];
        if (!isset($settings['bo_web_hook_enable'])) {
            throw new \DomainException('Not isset settings bo_web_hook_enable.');
        }

        $boUrl = Yii::$app->params['backOffice']['serverUrl'];
        if (!$boUrl) {
            throw new \DomainException('Not isset settings backOffice.serverUrl');
        }

        $boWhEndpoint = Yii::$app->params['backOffice']['webHookEndpoint'];
        if (!$boWhEndpoint) {
            throw new \DomainException('Not isset settings backOffice.webHookEndpoint');
        }

        $response = self::sendRequest2(
            $boWhEndpoint,
            array_merge(
                ['type' => $type],
                $data
            ),
            'POST',
            30,
            $boUrl,
            false
        );

        if (!$response->isOk) {
            \Yii::error([
                'message' => 'BO Webhook server error',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'BackOffice:wh');
            throw new \DomainException('BO Webhook server error.');
        }

        $data = $response->data;

        if (!$data) {
            \Yii::error([
                'message' => 'BO response Data is empty',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'BackOffice:wh');
            throw new \DomainException('BO response Data is empty.');
        }

        if (!is_array($data)) {
            \Yii::error([
                'message' => 'BO response Data type is invalid',
                'type' => $type,
                'data' => $data,
                'content' => VarDumper::dumpAsString($response->content),
            ], 'BackOffice:wh');
            throw new \DomainException('BO response Data type is invalid.');
        }

        return $data;
    }

    public static function whReprotection(array $data): array
    {
        return self::wh('reprotection', $data);
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
                        throw new Exception('Url: ' . $uri . ' , BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
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

    /**
     * @param array $data
     * @return mixed
     */
    public static function orderUpdateWebhook(array $data)
    {
        $endpoint = SettingHelper::getWebhookOrderUpdateBOEndpoint();

        if ($endpoint) {
            try {
                $response = self::sendRequest2($endpoint, $data);

                if ($response->isOk) {
                    $result = $response->data;
                    if ($result && is_array($result)) {
                        return $result;
                    }
                } else {
                    throw new Exception('Endpoint: ' . $endpoint . ' , BO request Error: ' . VarDumper::dumpAsString($response->content), $response->statusCode);
                }
            } catch (\Throwable $exception) {
                $code = $exception->getCode();
                \Yii::error($exception->getMessage(), 'BackOffice:orderUpdateWebhook');
                if ($code < 500 && $code !== 404) {
                    throw new \RuntimeException($exception->getMessage());
                }
            }
        } else {
            \Yii::error('Not provided endpoint', 'BackOffice:orderUpdateWebhook');
        }
    }

    public static function reprotectionCustomerDecisionConfirm(string $bookingId, array $quote): bool
    {
        return self::reprotectionCustomerDecision($bookingId, 'confirm', $quote);
    }

    public static function reprotectionCustomerDecisionModify(string $bookingId, array $quote): bool
    {
        return self::reprotectionCustomerDecision($bookingId, 'confirm', $quote);
    }

    public static function reprotectionCustomerDecisionRefund(string $bookingId): bool
    {
        return self::reprotectionCustomerDecision($bookingId, 'refund', []);
    }

    private static function reprotectionCustomerDecision(string $bookingId, string $type, array $quote): bool
    {
        if (!$bookingId) {
            throw new \DomainException('Booking ID is empty');
        }
        if (!in_array($type, ['confirm', 'refund'])) {
            throw new \DomainException('Undefined Type');
        }
        if ($type === 'confirm' && !$quote) {
            throw new \DomainException('Quote is empty');
        }

        $request = [
            'bookingId' => $bookingId,
            'type' => $type,
        ];
        if ($quote) {
            $request['flightQuote'] = $quote;
        }

//        VarDumper::dump($request);die;

        try {
            $response = self::sendRequest2(
                'flight-request/reprotection-decision',
                $request,
                'POST',
                30,
                Yii::$app->params['backOffice']['serverUrlV3']
            );

            if (!$response->isOk) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision server error',
                    'bookingId' => $bookingId,
                    'type' => $type,
                    'quote' => $quote,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:serverError');
                return false;
            }

            $data = $response->data;

            if (!$data) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision data is empty',
                    'bookingId' => $bookingId,
                    'type' => $type,
                    'quote' => $quote,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataIsEmpty');
                return false;
            }

            if (!is_array($data)) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision response Data type is invalid',
                    'bookingId' => $bookingId,
                    'type' => $type,
                    'quote' => $quote,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataIsInvalid');
                return false;
            }

            // todo change to correct data property
            if (!isset($response->data['data']['success'])) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision response Data object error',
                    'bookingId' => $bookingId,
                    'type' => $type,
                    'quote' => $quote,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataObjectInvalid');
                return false;
            }

            return (bool)($response->data['data']['success']);
        } catch (\Throwable $exception) {
            \Yii::error(AppHelper::throwableLog($exception, true), 'BackOffice:reprotectionCustomerDecision');
            return false;
        }
    }
}
