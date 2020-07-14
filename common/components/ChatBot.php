<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 22/06/2020
 * Time: 11:05 AM
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ChatBot
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 *
 * @property Request $request
 */

class ChatBot extends Component
{
    public string $url;
    public string $username;
    public string $password;

    private Request $request;

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
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'ChatBot::initRequest:Exception');
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
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []) : Response
    {
        $url = $this->url . $action;

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
            $this->request->setOptions($options);
        }

        return $this->request->send();
    }


    /**
     * @param string $rid
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function endConversation(string $rid) : array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'rid' => $rid,
        ];

        $response = $this->sendRequest('livechat/end-conversation', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString(['rid' => $rid, 'error' => $out['error']], 10), 'ChatBot:endConversation');
        }

        return $out;
    }

    /**
     * @param string $rid
     * @param string $userId
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function assignAgent(string $rid, string $userId) : array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'rid' => $rid,
            'userId' => $userId
        ];

        $response = $this->sendRequest('livechat/assign-agent', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:assignAgent');
        }

        return $out;
    }

    public function sendMessage(array $data, array $headers = []) : array
    {
        $out = ['error' => false, 'data' => []];

        $response = $this->sendRequest('chat.sendMessage', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:sendMessage');
        }

        return $out;
    }
}