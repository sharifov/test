<?php

namespace common\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\helpers\VarDumper;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class AirSearchService
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property string $searchQuoteEndpoint
 * @property string $searchQuoteByKeyEndpoint
 * @property Request $request
 */
class AirSearchService extends Component
{
    public string $username;
    public string $password;
    public string $url;
    public string $searchQuoteEndpoint;
    public string $searchQuoteByKeyEndpoint;

    public array $options = [CURLOPT_ENCODING => 'gzip'];
    public Request $request;

    public function init(): void
    {
        parent::init();
        $this->initRequest();
    }

    /**
     * @return bool
     */
    private function initRequest(): bool
    {
        $authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            $this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'AirSearchService::initRequest:Throwable');
        }

        return false;
    }

    /**
     * @param string $action
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @param string|null $format
     * @return \yii\httpclient\Response
     * @throws Exception
     */
    public function sendRequest(
        string $action = '',
        array $data = [],
        string $method = 'post',
        array $headers = [],
        array $options = [],
        ?string $format = null
    ): Response {
        $this->request->setMethod($method)
            ->setUrl($this->url . $action)
            ->setData($data);

        if ($headers) {
            $this->request->addHeaders($headers);
        }
        $this->request->setOptions(ArrayHelper::merge($this->options, $options));
        if (isset(\Yii::$app->params['additionalCurlOptions'])) {
            $this->request->addOptions(\Yii::$app->params['additionalCurlOptions']);
        }
        if ($format) {
            $this->request->setFormat($format);
        }

        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('air_search', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('air_search', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }


    /**
     * @param int $count
     * @param string $code
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public function generateCoupons(int $count = 0, string $code = '')
    {
        $params = [
            'nr' => $count,
            'code' => $code
        ];

        $response = $this->sendRequest('v1/discounts/coupons', $params, 'get');

        if ($response->isOk) {
            return $response->data;
        }

        \Yii::error(
            'Params: ' . VarDumper::dumpAsString($params, 10) .
            ' Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::generateCoupons'
        );
        return null;
    }


    /**
     * @param string $code
     * @return mixed|null
     * @throws Exception
     */
    public function validateCoupon(string $code)
    {
        $params = [
            'code' => $code
        ];

        $response = $this->sendRequest('v1/discounts/coupons/validate', $params, 'get');

        if ($response->isOk) {
            return $response->data;
        }

        \Yii::error(
            'Code: ' . $code .
            ', Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::validateCoupon'
        );
        return null;
    }

    /**
     * @param array $params
     * @return mixed|null
     * @throws Exception
     */
    public function getCoupons(array $params)
    {
        $result = null;
        $response = $this->sendRequest('v1/discounts/coupons', $params, 'get');

        if ($response->isOk) {
            return $response->data;
        }
        \Yii::error(
            'Params: ' . VarDumper::dumpAsString($params, 10) .
            ' Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::getCoupons'
        );
        return null;
    }

    /**
     * @param string $ip
     * @return mixed|null
     * @throws Exception
     */
    public function checkExcludeIp(string $ip)
    {
        $response = $this->sendRequest('airline/ip-check/' . $ip, [], 'get');

        if ($response->isOk) {
            return $response->data;
        }

        \Yii::error(
            'Ip: ' . $ip . ' Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::checkIp'
        );
        return null;
    }

    /**
     * @param array $params
     * @param string $method
     * @return array
     * @throws Exception
     */
    public function searchQuotes(array $params, string $method = 'GET'): array
    {
        $result = ['data' => [], 'error' => ''];
        $response = $this->sendRequest($this->searchQuoteEndpoint, $params, $method);

        if ($response->isOk) {
            $result['data'] = $response->data;
        } else {
            $result['error'] = $response->content;
        }
        return $result;
    }

    /**
     * @param string $cid
     * @param string $key
     * @param string $method
     * @return array
     * @throws Exception
     */
    public function searchQuoteByKey(string $cid, string $key, string $method = 'GET'): array
    {
        $result = ['data' => [], 'error' => ''];
        $url = $this->searchQuoteByKeyEndpoint . '/' . $cid . '/' . $key;
        $response = $this->sendRequest($url, [], $method);

        if ($response->isOk) {
            $result['data'] = $response->data;
        } else {
            $result['error'] = $response->content;
        }
        return $result;
    }
}
