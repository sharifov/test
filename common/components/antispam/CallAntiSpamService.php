<?php

/**
 * Created
 * User: alexandr
 * Date: 10/09/21
 * Time: 9:05 AM
 */

namespace common\components\antispam;

use common\components\Metrics;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class CallAntiSpamService
 * @package common\components\antispam
 *
 * @property string $host
 * @property int $port
 * @property int $timeout
 * @property Request $request
 */

class CallAntiSpamService extends Component
{
    public string $host         = 'localhost';
    public int $port            = 8001;
    public int $timeout         = 3;

    private Request $request;

    public function init(): void
    {
        parent::init();
        $this->initRequest();
    }


    /**
     * @return void
     */
    private function initRequest(): void
    {
        //$authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            //$this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return;
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'CallAntiSpamService::initRequest:Exception');
        }
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
        $url = $this->host;
        if ($this->port) {
            $url .= ':' . $this->port;
        }
        $url .= $action;


        //$options = ['RETURNTRANSFER' => 1];
//        VarDumper::dump($url);die;

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData($data);

        if ($headers) {
            $this->request->addHeaders($headers);
        }

        // $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->addOptions($options);
        }

//        if (isset(Yii::$app->params['additionalCurlOptions'])) {
//            $this->request->addOptions(Yii::$app->params['additionalCurlOptions']);
//        }

        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('callAntiSpam', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('callAntiSpam', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }


    /**
     * @param CallAntiSpamDto $dto
     * @return array
     * @throws Exception
     */
    public function checkData(CallAntiSpamDto $dto): array
    {
        $out = ['error' => false, 'data' => []];

        $data = $dto->getData();
        $response = $this->sendRequest('', $data, 'post', [], [
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        if ($response->isOk) {
            if (isset($response->data['Label'], $response->data['Score'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response Label or Score';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CallAntiSpamService::checkData');
        }

        return $out;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function ping(): bool
    {
        $response = $this->sendRequest('/docs', [], 'get');
        return $response->isOk;
    }
}
