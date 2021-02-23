<?php

namespace modules\cruise\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ApiCruiseService
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */
class ApiCruiseService extends Component
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
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiHotelService::initRequest:Throwable');
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

    public function cruiseSearch(Params $params): array
    {
        $data = [
            'date_from' => $params->date_from,
            'date_to' => $params->date_to,
            'destination' => $params->destination,
            'adult' => $params->adult,
            'child' => $params->child
        ];

        try {
            $response = $this->sendRequest('product/cruise-search', $data);
            if ($response->isOk) {
                if (!isset($response->data['status'])) {
                    $message = 'Not found response status.';
                    $this->log($message, $response);
                    throw new \DomainException('Search service: ' . $message);
                }
                if ($response->data['status'] === 'ok') {
                    if (array_key_exists('cruises', $response->data['data'])) {
                        return $response->data['data']['cruises'];
                    }
                    $message = 'Not found in response array data key [data][cruises].';
                    $this->log($message, $response);
                    throw new \DomainException('Search service: ' . $message);
                }
                if (isset($response->data['error'])) {
                    throw new \DomainException('Search service: ' . $response->data['error']);
                }
                $this->log('Undefined response.', $response);
                throw new \DomainException('Search service: Server error.');
            }
            $this->log('Search Service error.', $response);
            throw new \DomainException('Search service: Server error.');
        } catch (\DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Yii::error(VarDumper::dumpAsString($e, 10), 'Component:ApiCruiseService::cruiseSearch');
            throw new \DomainException('ApiCruiseService error: ' . $e->getMessage());
        }
    }

    private function log($message, $response): void
    {
        \Yii::error([
            'message' => $message,
            'data' => VarDumper::dumpAsString($response->data),
            'content' => VarDumper::dumpAsString($response->content),
            'status' => $response->statusCode,
        ], 'Component:ApiCruiseService::cruiseSearch');
    }
}
