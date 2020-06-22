<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 22/06/2020
 * Time: 11:05 AM
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class RocketChat
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 *
 * @property string $currentUserId
 * @property string $currentAuthToken
 *
 * @property array $systemAuthData
 * @property Request $request
 */

class RocketChat extends Component
{
    public string $url;
    public string $username;
    public string $password;

    private string $currentUserId;
    private string $currentAuthToken;

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
        //$authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            //$this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'RocketChat::initRequest:Exception');
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
     * @param bool $extra
     * @param string|null $sourceCurrencyCode
     * @param array $rateCurrencyList
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function systemLogin() : array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'user' => $this->username,
            'password' => $this->password
        ];

        $response = $this->sendRequest('login', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data['data'])) {
                if (!empty($response->data['status'] === 'success')) {
                    $out['data'] = $response->data['data'];

                } else {
                    $out['error'] = $response->content;
                    \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:systemLogin');
                }
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:systemLogin');
        }

        return $out;
    }


    /**
     * @param string $username
     * @param string $password
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function login(string $username, string $password) : array
    {
        $out = ['error' => false, 'data' => []];
        $data = [
            'user' => $username,
            'password' => $password
        ];

        $response = $this->sendRequest('login', $data, 'post');

        if ($response->isOk) {
            if (!empty($response->data['data'])) {
                if (!empty($response->data['status'] === 'success')) {
                    $out['data'] = $response->data['data'];

                } else {
                    $out['error'] = $response->content;
                    \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:login');
                }
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:login');
        }

        return $out;
    }


    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function getSystemAuthData(): array
    {
        $cache = \Yii::$app->cache;
        $key = 'rocket_chat_system_authToken';

        $authData = $cache->get($key);

        if ($authData === false) {
            $response = $this->systemLogin();
            if (!empty($response['data']['userId']) && !empty($response['data']['authToken'])) {
                $authData = [
                    'userId' => $response['data']['userId'],
                    'authToken' => $response['data']['authToken']
                ];
                $cache->set($key, $authData, 3600 * 24 * 30);
            }
        }
        return $authData;
    }

    /**
     * @return mixed|null
     * @throws \yii\httpclient\Exception
     */
    public function getSystemUserId()
    {
        $authData = $this->getSystemAuthData();
        if (!empty($authData['userId'])) {
            return $authData['userId'];
        }
        return null;
    }

    /**
     * @return mixed|null
     * @throws \yii\httpclient\Exception
     */
    public function getSystemAuthToken()
    {
        $authData = $this->getSystemAuthData();
        if (!empty($authData['authToken'])) {
            return $authData['authToken'];
        }
        return null;
    }

    /**
     * @param string $userId
     * @param string $authToken
     */
    public function setCurrentUser(string $userId, string $authToken)
    {
        $this->currentUserId = $userId;
        $this->currentAuthToken = $authToken;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $name
     * @param string $email
     * @param array|string[] $roles
     * @param bool $active
     * @param bool $joinDefaultChannels
     * @return array
     * @throws \yii\httpclient\Exception
     *
     *
     *
     * [
        'user' => [
            '_id' => 'KJDSzxEghzYnBxgMR'
            'createdAt' => '2020-06-22T11:49:06.284Z'
            'username' => 'alex.connor3'
            'emails' => [
                0 => [
                    'address' => 'alex.connor3@techork.com'
                    'verified' => false
                ]
            ]
            'type' => 'user'
            'status' => 'offline'
            'active' => true
            '_updatedAt' => '2020-06-22T11:49:06.763Z'
            'roles' => [
                0 => 'user'
                1 => 'livechat-agent'
            ]
            'name' => 'alex.connor2'
            'settings' => []
        ]
        'success' => true
    ]
     *
     *
     */

    public function createUser(string $username, string $password, string $name, string $email, array $roles = ["user", "livechat-agent"], bool $active = true, bool $joinDefaultChannels = false): array
    {

        $out = ['error' => false, 'data' => []];
        $headers = [
            'X-User-Id' => $this->getSystemUserId(),
            'X-Auth-Token' => $this->getSystemAuthToken()
        ];

        $data['username'] = $username;
        $data['password'] = $password;
        $data['name'] = $name;
        $data['email'] = $email;
        $data['roles'] = $roles;
        $data['active'] = $active;
        $data['joinDefaultChannels'] = $joinDefaultChannels;

        $response = $this->sendRequest('users.create', $data, 'post', $headers);

        if ($response->isOk) {
            //VarDumper::dump($response->data, 10, true); exit;

            if (!empty($response->data['user'])) {
                $out['data'] = $response->data['user'];
            } else {
                $out['error'] = 'Not found in response array data key [user]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:createUser');
        }

        return $out;
    }


    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function getAllDepartments(): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = [
            'X-User-Id' => $this->getSystemUserId(),
            'X-Auth-Token' => $this->getSystemAuthToken()
        ];

       $data = [];

        $response = $this->sendRequest('livechat/department', $data, 'get', $headers);

        if ($response->isOk) {

            if (!empty($response->data['departments'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [departments]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:getAllDepartments');
        }

        return $out;
    }


    /**
     * @param string $rid
     * @param array $attachments
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function sendMessage(string $rid, array $attachments): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = [
            'X-User-Id' => $this->currentUserId,
            'X-Auth-Token' => $this->currentAuthToken
        ];

        $message['rid'] = $rid;

        $data['message'] = $message;
        $data['attachments'] = $attachments;
        $data['customTemplate'] = 'carousel';

        $response = $this->sendRequest('chat.sendMessage', $data, 'get', $headers);

        if ($response->isOk) {

            if (!empty($response->data['departments'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [departments]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:getAllDepartments');
        }

        return $out;
    }

    //public function

}