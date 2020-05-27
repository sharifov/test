<?php
namespace common\components;

use yii\base\Component;
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
     * @return \yii\httpclient\Response
     * @throws \yii\httpclient\Exception
     */
    private function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []) : Response
    {
        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setUrl($this->url . $action)
            ->setData($data);

        if($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if($options) {
            $this->request->setOptions($options);
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

        \Yii::error('Params: ' . VarDumper::dumpAsString($params, 10) . ' Error: ' . VarDumper::dumpAsString($response->content, 10), 'AirSearchService::getCoupons');
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

        \Yii::error('Code: ' . $code . ', Error: ' . VarDumper::dumpAsString($response->content, 10), 'AirSearchService::validateCoupon');
        return null;
    }

}