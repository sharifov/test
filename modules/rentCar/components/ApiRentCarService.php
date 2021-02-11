<?php

namespace modules\rentCar\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ApiRentCarService
 * @package modules\RentCar\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */

class ApiRentCarService extends Component
{
    public $url;
    public $username;
    public $password;
    public $options = [CURLOPT_ENCODING => 'gzip'];

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
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiRentCarService::initRequest:Throwable');
        }

        return false;
    }

    /**
     * @param string $action
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return Response
     * @throws \yii\httpclient\Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = $this->url . $action;
        /* @var $this->request Client */
        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        $this->setFormatJson($method);
        $this->request->setOptions(ArrayHelper::merge($this->options, $options));
        if ($headers) {
            $this->request->addHeaders($headers);
        }
        return $this->request->send();
    }

    /**
     * @param string $method
     */
    protected function setFormatJson(string $method): void
    {
        $method = strtolower($method);
        if ($method === 'post' || $method === 'delete') {
            $this->request->setFormat(Client::FORMAT_JSON);
        }
    }

    public function search(
        string $pickUpCode,
        string $pickUpDate,
        ?string $pickUpTime = null,
        ?string $dropOffTime = null,
        ?string $dropOffCode = null,
        ?string $dropOffDate = null
    ): array {
        $out = ['error' => false, 'data' => []];

        $data['date_from'] = $pickUpDate;
        $data['location'] = $pickUpCode;
        if ($pickUpTime) {
            $data['start_time'] = $pickUpTime;
        }
        if ($dropOffTime) {
            $data['end_time'] = $dropOffTime;
        }
        if ($dropOffDate) {
            $data['date_to'] = $dropOffDate;
        }
        if ($dropOffCode) {
            /* TODO::  */
        }

        try {
            $response = $this->sendRequest('product/rentcar-search', $data, 'post');

            if ($response->isOk && !isset($response->data['error'])) {
                if (isset($response->data['data']['carSearch']['listings'])) {
                    $out['data'] = $response->data['data']['carSearch']['listings'];
                } else {
                    $out['error'] = 'Not found in response array data key [carSearch]';
                }
            } elseif (isset($response->data['error'])) {
                $out['error'] = VarDumper::dumpAsString($response->data['error']);
                \Yii::error($out['error'], 'Component:ApiRentCarService::search');
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:ApiRentCarService::search');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'Component:ApiRentCarService::throwable');
            $out['error'] = 'ApiRentCarService error: ' . $throwable->getMessage();
        }

        return $out;
    }
}
