<?php

namespace common\components\ga;

use yii\base\Component;
use src\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class GaRequestService
 *
 * @property string $url
 * @property int $v
 */
class GaRequestService extends Component
{
    public $url;
    public $v;

    public bool $debugMod = false;
    public string $debugUrl = 'https://www.google-analytics.com/debug/collect';
    public array $options = [CURLOPT_ENCODING => 'gzip'];

    private Request $curlRequest;

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
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->curlRequest = $client->createRequest();
            $this->curlRequest->setUrl($this->url);

            return true;
        } catch (Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'GaRequestService::initRequest:Throwable'
            );
        }
        return false;
    }

    /**
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @param null|string $format
     * @return Response
     */
    public function sendRequest(
        array $data = [],
        string $method = 'post',
        array $headers = [],
        array $options = [],
        ?string $format = null
    ): Response {
        $this->curlRequest->setMethod($method);

        if ($data) {
            $this->curlRequest->addData($data);
        }
        if ($headers) {
            $this->curlRequest->addHeaders($headers);
        }

        $this->curlRequest->setOptions(ArrayHelper::merge($this->options, $options));
        if (isset(Yii::$app->params['additionalCurlOptions'])) {
            $this->curlRequest->addOptions(Yii::$app->params['additionalCurlOptions']);
        }
        if (!empty($format)) {
            $this->curlRequest->setFormat($format);
        }
        if ($this->debugMod) {
            $this->curlRequest->setUrl($this->debugUrl);
        }
        return $this->curlRequest->send();
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        $response = $this->sendRequest([], 'get');
        return $response->statusCode === '401';
    }
}
