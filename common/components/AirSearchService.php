<?php
namespace common\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\helpers\VarDumper;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class AirSearchService
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */
class AirSearchService extends Component
{
    public string $username;
    public string $password;
    public string $url;
    public array $options = [CURLOPT_ENCODING => 'gzip'];
    public Request $request;

    public function init() : void
    {
        parent::init();
        $this->initRequest();
    }

    /**
     * @return bool
     */
    private function initRequest() : bool
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
     * @throws \yii\httpclient\Exception
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
        return $this->request->send();
    }


    /**
     * @param int $count
     * @param string $code
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
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

        \Yii::error('Params: ' . VarDumper::dumpAsString($params, 10) .
            ' Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::generateCoupons');
        return null;
    }


    /**
     * @param string $code
     * @return mixed|null
     * @throws \yii\httpclient\Exception
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

        \Yii::error('Code: ' . $code .
            ', Error: ' . VarDumper::dumpAsString($response->content, 10),
            'AirSearchService::validateCoupon');
        return null;
    }

    /**
     * @param array $params
     * @return mixed|null
     * @throws \yii\httpclient\Exception
     */
    public function getCoupons(array $params)
    {
        $result = null;
        $response = $this->sendRequest('v1/discounts/coupons', $params, 'get');

        if ($response->isOk) {
            return $response->data;
        }
        \Yii::error('Params: ' . VarDumper::dumpAsString($params, 10) .
            ' Error: ' . VarDumper::dumpAsString($response->content, 10),
            'SearchService::getCoupons');
        return null;
    }

}