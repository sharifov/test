<?php

namespace common\components;

use common\models\Project;
use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\entities\cases\CaseEventLog;
use src\exception\BoResponseException;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\logger\behaviors\filters\creditCard\V5;
use webapi\src\request\BoRequestDataHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\CurlTransport;

class BackOffice
{
    /**
     * @param string $endpoint
     * @param string $type
     * @param string|null $fields
     * @return mixed
     * @throws \JsonException
     * @throws \Throwable
     */
    public static function sendRequest(string $endpoint, string $type = 'GET', string $fields = null)
    {
        $url = sprintf('%s/%s', Yii::$app->params['backOffice']['serverUrl'], $endpoint);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type === 'POST') {
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
        try {
            return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'result' => $result,
                'url' => $url,
                'fields' => $fields,
            ]);
            \Yii::error($message, 'BackOffice::sendRequest:Throwable');
            throw $throwable;
        }
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
            \Yii::error('Not provided endpoint [settings][webhook_order_update_bo_endpoint]', 'BackOffice:orderUpdateWebhook');
        }
    }

    public static function reprotectionCustomerDecisionConfirm(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return self::reprotectionCustomerDecision($projectId, $bookingId, 'confirm', $quote, $reprotectionQuoteGid);
    }

    public static function reprotectionCustomerDecisionModify(int $projectId, string $bookingId, array $quote, string $reprotectionQuoteGid): bool
    {
        return self::reprotectionCustomerDecision($projectId, $bookingId, 'confirm', $quote, $reprotectionQuoteGid);
    }

    public static function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool
    {
        return self::reprotectionCustomerDecision($projectId, $bookingId, 'refund', [], null);
    }

    private static function reprotectionCustomerDecision(int $projectId, string $bookingId, string $type, array $quote, ?string $reprotectionQuoteGid): bool
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

        $projectApiKey = Project::find()->select(['api_key'])->andWhere(['id' => $projectId])->scalar();
        if (!$projectApiKey) {
            throw new \DomainException('Not found API key. ProjectId: ' . $projectId);
        }

        $request = [
            'apiKey' => $projectApiKey,
            'bookingId' => $bookingId,
            'type' => $type,
        ];
        if ($reprotectionQuoteGid) {
            $request['reprotection_quote_gid'] = $reprotectionQuoteGid;
        }
        if ($quote) {
            $request['flightQuote'] = $quote;
        }
        if ($reprotectionQuoteGid) {
            $productQuote = ProductQuote::find()->where(['pq_gid' => $reprotectionQuoteGid])->limit(1)->one();
            $request['additionalInfo'] = BoRequestDataHelper::prepareAdditionalInfoToBoRequest($productQuote);
        }

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
                    'request' => $request,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:serverError');
                return false;
            }

            $data = $response->data;

            if (!$data) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision data is empty',
                    'request' => $request,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataIsEmpty');
                return false;
            }

            if (!is_array($data)) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision response Data type is invalid',
                    'request' => $request,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataIsInvalid');
                return false;
            }
            if (!isset($data['success'])) {
                \Yii::error([
                    'message' => 'BO reprotection customer decision is not isset "success" in response',
                    'request' => $request,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:dataObjectInvalid');
                return false;
            }
            if ((bool) $data['success'] !== true) {
                \Yii::warning([
                    'message' => 'BO reprotection customer decision is not success response',
                    'request' => $request,
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:reprotectionCustomerDecision:not:success');
                self::addWarningToCaseEventLog($reprotectionQuoteGid, $data);
                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            \Yii::error(AppHelper::throwableLog($exception, true), 'BackOffice:reprotectionCustomerDecision');
            return false;
        }
    }

    public static function voluntaryExchange(array $request): ?array
    {
        if (empty(SettingHelper::getVoluntaryExchangeBoEndpoint())) {
            throw new BoResponseException('BO endpoint is empty', BoResponseException::BO_WRONG_ENDPOINT);
        }

        try {
            $response = self::sendRequest2(
                SettingHelper::getVoluntaryExchangeBoEndpoint(),
                $request,
                'POST',
                30,
                Yii::$app->params['backOffice']['serverUrlV3']
            );

            $data = $response->data;
            if (!empty($data['message']) && mb_stripos($data['message'], 'page not found') !== false) {
                \Yii::error([
                    'message' => 'BO wrong endpoint: ' . SettingHelper::getVoluntaryExchangeBoEndpoint(),
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:voluntaryExchange:wrongEndpoint');
                throw new BoResponseException('BO wrong endpoint', BoResponseException::BO_WRONG_ENDPOINT);
            }
            if (!isset($response->content) || !$content = JsonHelper::decode($response->content)) {
                \Yii::error([
                    'message' => 'BO voluntaryExchange server error. Content not found',
                    'content' => VarDumper::dumpAsString($response),
                    'request' => (new CreditCardFilter())->filterData($request),
                ], 'BackOffice:voluntaryExchange:serverError');
                throw new BoResponseException('BO voluntaryExchange server error. Content not found', BoResponseException::BO_DATA_IS_EMPTY);
            }
            if (!$response->isOk) {
                \Yii::error([
                    'message' => 'BO voluntaryExchange server error',
                    'content' => VarDumper::dumpAsString($content),
                    'request' => (new CreditCardFilter())->filterData($request),
                ], 'BackOffice:voluntaryExchange:serverError');
                return $content;
            }
            if (!isset($content['status'])) {
                \Yii::error([
                    'message' => 'BO voluntaryExchange response - status not found in response',
                    'content' => VarDumper::dumpAsString($content),
                    'request' => (new CreditCardFilter())->filterData($request),
                ], 'BackOffice:voluntaryExchange:statusNotFound');
                throw new BoResponseException('BO voluntaryExchange response - status not found in response', BoResponseException::BO_RESPONSE_DATA_TYPE_IS_INVALID);
            }
            return $content;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public static function voluntaryRefund(array $requestData, string $endpoint): array
    {
        try {
            $response = self::sendRequest2(
                $endpoint,
                $requestData,
                'POST',
                30,
                Yii::$app->params['backOffice']['serverUrlV3']
            );
        } catch (\Throwable $exception) {
            \Yii::error([
                'message' => $exception->getMessage(),
            ], 'BackOffice:voluntaryRefund');
            throw new BoResponseException('BO voluntaryRefund server error', BoResponseException::BO_SERVER_ERROR);
        }

        $data = $response->data;
        if (!$data) {
            \Yii::error([
                    'message' => 'BO voluntaryRefund data is empty',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => (new CreditCardFilter())->filterData($requestData),
                ], 'BackOffice:voluntaryRefund:dataIsEmpty');
            throw new BoResponseException('BO voluntaryRefund data is empty', BoResponseException::BO_DATA_IS_EMPTY);
        }
        if (!is_array($data)) {
            \Yii::error([
                    'message' => 'BO voluntaryRefund response Data type is invalid',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => (new CreditCardFilter())->filterData($requestData),
                ], 'BackOffice:voluntaryRefund:dataIsInvalid');
            throw new BoResponseException('BO voluntaryRefund response Data type is invalid', BoResponseException::BO_RESPONSE_DATA_TYPE_IS_INVALID);
        }
        if (!empty($data['message']) && mb_stripos($data['message'], 'page not found') !== false) {
            \Yii::error([
                'message' => 'BO wrong endpoint: ' . $endpoint,
                'content' => VarDumper::dumpAsString($response->content),
                'requestData' => (new CreditCardFilter())->filterData($requestData),
            ], 'BackOffice:voluntaryRefund:wrongEndpoint');
            throw new BoResponseException('BO wrong endpoint', BoResponseException::BO_WRONG_ENDPOINT);
        }
        return $data;
    }

    public static function getExchangeData(array $requestData, string $endpoint = 'flight-request/get-exchange-data'): array
    {
        try {
            $response = self::sendRequest2(
                $endpoint,
                $requestData,
                'POST',
                30,
                Yii::$app->params['backOffice']['serverUrlV3']
            );
        } catch (\Throwable $exception) {
            \Yii::error(AppHelper::throwableLog($exception, true), 'BackOffice:getExchangeData');
            throw new BoResponseException('BO "Get Exchange Data" server error', BoResponseException::BO_SERVER_ERROR);
        }

        if (!$response->isOk) {
            \Yii::error([
                    'message' => 'BO (' . $endpoint . ') server error',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => $requestData,
                ], 'BackOffice:getExchangeData:ServerError');
            throw new BoResponseException('BO "Get Exchange Data" server error', BoResponseException::BO_SERVER_ERROR);
        }
//        \Yii::info(var_export($response, true), 'info\FlightQuoteController:actionCreateVoluntaryQuote:LOG');
        $data = $response->data;
        if (!$data) {
            \Yii::error([
                    'message' => 'BO (' . $endpoint . ') data is empty',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => $requestData,
                ], 'BackOffice:getExchangeData:dataIsEmpty');
            throw new BoResponseException('BO "Get Exchange Data" data is empty', BoResponseException::BO_DATA_IS_EMPTY);
        }
        if (!is_array($data)) {
            \Yii::error([
                    'message' => 'BO  (' . $endpoint . ') response Data type is invalid',
                    'content' => VarDumper::dumpAsString($response->content),
                ], 'BackOffice:getExchangeData:dataIsInvalid');
            throw new BoResponseException('BO "Get Exchange Data" response Data type is invalid', BoResponseException::BO_RESPONSE_DATA_TYPE_IS_INVALID);
        }

        $errors = $data['errors'] ?? null;
        $success = $data['success'] ?? null;
        if ($success === false) {
            \Yii::warning([
                    'message' => 'BO  (' . $endpoint . ') response not success',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => $requestData,
                ], 'BackOffice:getExchangeData:notSuccess');
            throw new BoResponseException('BO "Get Exchange Data" response not success. ' . $errors, BoResponseException::BO_RESPONSE_NOT_SUCCESS);
        }
        if (!array_key_exists('allow', $data)) {
            \Yii::warning([
                    'message' => 'BO  (' . $endpoint . ') response Data "allow" key not found',
                    'content' => VarDumper::dumpAsString($response->content),
                    'requestData' => $requestData,
                ], 'BackOffice:getExchangeData:dataIsInvalid');
            throw new BoResponseException('BO "Get Exchange Data" response Data allow key not found', BoResponseException::BO_RESPONSE_NOT_SUCCESS);
        }
        if (!empty($data['message']) && mb_stripos($data['message'], 'page not found') !== false) {
            \Yii::warning([
                'message' => 'BO wrong endpoint: ' . $endpoint,
                'content' => VarDumper::dumpAsString($response->content),
                'requestData' => $requestData,
            ], 'BackOffice:getExchangeData:wrongEndpoint');
            throw new BoResponseException('BO wrong endpoint', BoResponseException::BO_WRONG_ENDPOINT);
        }
        return $data;
    }

    private static function addWarningToCaseEventLog(?string $reProtectionQuoteGid, array $data): void
    {
        if (!$reProtectionQuoteGid || !($errorMsg = $data['errors'] ?? null) || !is_string($errorMsg)) {
            return;
        }
        if (!$reProtectionQuote = ProductQuote::find()->where(['pq_gid' => $reProtectionQuoteGid])->limit(1)->one()) {
            return;
        }
        if (!$originProductQuote = $reProtectionQuote->relateParent) {
            return;
        }
        $productQuoteChange = ProductQuoteChange::find()
            ->byProductQuote($originProductQuote->pq_id)
            ->scheduleChangeType()
            ->orderBy(['pqc_id' => SORT_DESC])
            ->one();
        if ($productQuoteChange && ($case = $productQuoteChange->pqcCase)) {
            $case->addEventLog(
                CaseEventLog::RE_PROTECTION_CREATE,
                $errorMsg,
                ['reProtectionQuoteGid' => $reProtectionQuoteGid],
                CaseEventLog::CATEGORY_WARNING
            );
        }
    }
}
