<?php

namespace common\components\ga;

use yii\base\Component;
use sales\helpers\app\AppHelper;
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
 * @property string $tid
 */
class GaRequestService extends Component
{
    public $url;
    public $v;
    public $tid;

    public array $options = [CURLOPT_ENCODING => 'gzip'];

    private Request $curlRequest;

    public function init() : void
    {
        parent::init();
        $this->initRequest();
    }

    private function initRequest() : bool
    {
        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->curlRequest = $client->createRequest();
            $this->curlRequest->setUrl($this->url);
            $this->curlRequest->setData([
                'v' => $this->v,
                'tid' => $this->tid,
            ]);
            return true;
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaRequestService::initRequest:Throwable');
        }
        return false;
    }

    /**
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @param string|null $format
     * @return Response
     * @throws \yii\httpclient\Exception
     */
    public function sendRequest(
        array $data = [],
        string $method = 'post',
        array $headers = [],
        array $options = [],
        ?string $format = null
    ): Response
    {
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
        if ($format) {
            $this->curlRequest->setFormat($format);
        }
        return $this->curlRequest->send();
    }
}