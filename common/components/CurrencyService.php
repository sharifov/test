<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 11:05 AM
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class CurrencyService
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */

class CurrencyService extends Component
{
    public $url;
    public string $username;
    public string $password;

    private $request;

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
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'CurrencyService::initRequest:Exception');
        }

        return false;
    }

    /**
     * @param string $action
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return \yii\httpclient\Response
     * @throws \yii\httpclient\Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = $this->url . $action;

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        if ($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->setOptions($options);
        }

        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('currency', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('currency', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }


    /**
     * @param bool $extra
     * @param string|null $sourceCurrencyCode
     * @param array $rateCurrencyList
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function getRate(bool $extra, ?string $sourceCurrencyCode, array $rateCurrencyList = []): array
    {
        $out = ['error' => false, 'data' => []];
        $data = [];

        if ($sourceCurrencyCode) {
            $data['source'] = $sourceCurrencyCode;
        }

        if ($rateCurrencyList) {
            $data['currencies'] = implode(',', $rateCurrencyList);
        }

        if ($extra) {
            $data['extra'] = 'true';
        }


        $response = $this->sendRequest('rate', $data, 'get');

        if ($response->isOk) {
            if (isset($response->data['quotes'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [quotes]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CurrencyService::getRate');
        }

        return $out;
    }
}
