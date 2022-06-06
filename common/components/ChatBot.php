<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 22/06/2020
 * Time: 11:05 AM
 */

namespace common\components;

use src\auth\Auth;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
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
 * @property string $visitorsUrl
 * @property string $username
 * @property string $password
 *
 * @property Request $request
 */

class ChatBot extends Component
{
    public string $url;
    public string $visitorsUrl;
    public string $username;
    public string $password;

    private Request $request;

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
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $url = $this->url . $action;
        //$options = ['RETURNTRANSFER' => 1];

        $response = $this->send($url, $data, $method, $headers, $options);

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('chat_bot', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('chat_bot', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return Response
     * @throws \yii\httpclient\Exception
     */
    private function send(string $url, array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
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
     * @param string $visitorId
     * @param bool $shallowClose
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function endConversation(string $rid, string $visitorId, bool $shallowClose = true): array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'rid' => $rid,
            'visitorId' => $visitorId,
            'shallowClose' => $shallowClose
        ];

        $headers = \Yii::$app->rchat->getSystemAuthDataHeader();
        $response = $this->sendRequest('livechat/end-conversation', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
        }

        return $out;
    }

    /**
     * @param string $rid
     * @param string $visitorId
     * @param string $oldDepartment
     * @param string $newDepartment
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function transferDepartment(string $rid, string $visitorId, string $oldDepartment, string $newDepartment): array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'rid' => $rid,
            'visitorId' => $visitorId,
            'oldDepartment' => $oldDepartment,
            'newDepartment' => $newDepartment
        ];

        $headers = \Yii::$app->rchat->getSystemAuthDataHeader();
        $response = $this->sendRequest('livechat/transfer-department', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
        }

        return $out;
    }

    /**
     * @param string $rid
     * @param string $userId
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function assignAgent(string $rid, string $userId): array
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
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
//            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:assignAgent');
        }

        return $out;
    }

    public function createRoom(string $visitorId, string $channelId, ?string $message, string $userRcId, string $userRcToken): array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'visitorId' => $visitorId,
            'department' => $channelId,
            'userId' => $userRcId,
            'userToken' => $userRcToken,
            'message' => $message
        ];

        $response = $this->sendRequest('livechat/create-room', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
        }

        return $out;
    }

    public function sendMessage(array $data, array $headers = []): array
    {
        $out = ['error' => false, 'data' => []];

        $response = $this->sendRequest('chat.sendMessage', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
            //          \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:sendMessage');
        }

        return $out;
    }

    public function sendOffer(array $data, array $headers = []): array
    {
        $out = ['error' => false, 'data' => []];

        $response = $this->sendRequest('livechat/send-offer', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
            //          \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:sendMessage');
        }

        return $out;
    }

    public function getUserInfo(string $username)
    {
        $out = ['error' => false, 'data' => []];

        $data = [
            'username' => $username
        ];

        $headers = \Yii::$app->rchat->getSystemAuthDataHeader();
        $response = $this->sendRequest('users.info?' . http_build_query($data), $data, 'GET', $headers);

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
            //          \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ChatBot:sendMessage');
        }

        return $out;
    }

    public function sendNote(string $rid, string $message, string $alias): array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'rid' => $rid,
            'message' => $message,
            'alias' => $alias,
        ];

        $response = $this->sendRequest('livechat/send-note', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data)) {
                $out['data'] = $response->data;
            } else {
                $out['error']['message'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $this->parseErrorContent($response);
        }
        return $out;
    }

    private function parseErrorContent(Response $response): array
    {
        $result = json_decode($response->content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }
        return ['message' => strip_tags($response->content)];
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        $result = $this->getUserInfo($this->username);
        return isset($result['data']);
    }
}
