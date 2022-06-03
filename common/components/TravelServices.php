<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 21/07/2020
 * Time: 11:05 AM
 */

namespace common\components;

use src\model\airline\service\AirlineService;
use src\model\airportLang\service\AirportLangService;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class TravelServices
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 *
 * @property Request $request
 */

class TravelServices extends Component
{
    public string $url;
    public string $username;
    public string $password;

    private Request $request;

    /**
     *
     */
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
        try {
            $client = new Client(['baseUrl' => $this->url]);
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();

            if (empty($this->username) && empty($this->password)) {
                $authStr = base64_encode($this->username . ':' . $this->password);
                $this->request->setHeaders(['Authorization' => 'Basic ' . $authStr]);
            }
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'TravelServices::initRequest:Exception');
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
     * @throws Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = $action;

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setUrl($url)
            ->setData($data);

        if ($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->addOptions($options);
        }
        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('travel', ['type' => 'success', 'action' => strtok($action, '?')]);
        } else {
            $metrics->serviceCounter('travel', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }

    /**
     * @param int $lastUpdate
     * @param int $limit
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function airportExport(int $lastUpdate = 0, int $limit = 0, string $format = 'json'): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        if (!empty($limit)) {
            $data['limit'] = $limit;
        }

        if (!empty($format)) {
            $data['format'] = $format;
        }
        if (!empty($lastUpdate)) {
            $data['lastUpdate'] = $lastUpdate;
        }


        $params = http_build_query($data);
        $response = $this->sendRequest('airport/export?' . $params, [], 'get');

        if ($response->isOk) {
            if (isset($response->data['Data'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [Data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'TravelServices:airportExport');
        }

        return $out;
    }

    public function airportExportLocalized(int $lastUpdate = 0, int $pageLimit = 99999, int $pageIndex = 0, string $lang = '', string $format = 'json'): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        if (!empty($lang)) {
            $data['lang'] = $lang;
        }
        if (!empty($lastUpdate)) {
            $data['lastUpdate'] = $lastUpdate;
        }
        if (!empty($pageLimit)) {
            $data['pageLimit'] = $pageLimit;
        }
        if (!empty($pageIndex)) {
            $data['pageIndex'] = $pageIndex;
        }
        if (!empty($format)) {
            $data['format'] = $format;
        }

        $params = http_build_query($data);
        $response = $this->sendRequest(AirportLangService::SERVICE_ENDPOINT . '?' . $params, [], 'get');

        if ($response->isOk) {
            if (isset($response->data['Data'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [Data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'TravelServices:airportExportLocalized');
        }
        return $out;
    }

    public function airlineExport(int $lastUpdate = 0, int $limit = 99999, bool $ad = false, string $format = 'json'): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [];
        if (!empty($lastUpdate)) {
            $data['lastUpdate'] = $lastUpdate;
        }
        if (!empty($limit)) {
            $data['limit'] = $limit;
        }
        if (!empty($ad)) {
            $data['ad'] = $ad;
        }
        if (!empty($format)) {
            $data['format'] = $format;
        }

        $params = http_build_query($data);
        $response = $this->sendRequest(AirlineService::SERVICE_ENDPOINT . '?' . $params, [], 'get');

        if ($response->isOk) {
            if (isset($response->data['Data'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [Data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'TravelServices:airlineExport');
        }
        return $out;
    }

//    /**
//     * @return bool
//     */
//    public function ping(): bool
//    {
//        return false;
//    }
}
