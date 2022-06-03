<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 22/06/2020
 * Time: 11:05 AM
 */

namespace common\components;

use frontend\helpers\JsonHelper;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class RocketChat
 * @package common\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property string $host
 * @property string $apiServer
 * @property string $chatApiScriptUrl
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
    public string $host;
    public string $apiEntranceUrl = '/api/v1/';
    public string $apiServer;
    public string $chatApiScriptUrl;

    private string $currentUserId;
    private string $currentAuthToken;
    private array $systemAuthData = [];

    private Request $request;

    public function init(): void
    {
        parent::init();
        $this->url = $this->host . $this->apiEntranceUrl;
        $this->initRequest();
    }

    /**
     * @return bool
     */
    private function initRequest(): bool
    {
        $this->request = $this->getNewRequest();
        return $this->request != null;
    }

    /**
     * @return null|Request
     */
    private function getNewRequest(): ?\yii\httpclient\Request
    {
        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            return $client->createRequest();
        } catch (\Throwable $error) {
            \Yii::error(VarDumper::dumpAsString($error, 10), 'RocketChat::initRequest:Exception');
        }

        return null;
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
        $url = $this->url . $action;

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setUrl($url)
            ->setData($data);

        if ($headers) {
            $this->request->setHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->setOptions($options);
        }

        $response = $this->request->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('rocket_chat', ['type' => 'success', 'action' => $action]);
        } else {
            $metrics->serviceCounter('rocket_chat', ['type' => 'error', 'action' => $action]);
        }
        unset($metrics);

        return $response;
    }


    /**
     * @param bool $extra
     * @param string|null $sourceCurrencyCode
     * @param array $rateCurrencyList
     * @return array
     * @throws Exception
     */
    public function systemLogin(): array
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
     * @throws Exception
     */
    public function login(string $username, string $password): array
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
     * @param bool $cacheEnable
     * @return array
     * @throws Exception
     */
    public function getSystemAuthData(bool $cacheEnable = true): array
    {
        $cache = \Yii::$app->cache;
        $key = 'rocket_chat_system_authToken_' . md5($this->url);

        if (!$cacheEnable) {
            $cache->delete($key);
        }
        $authData = $cache->get($key);

        if ($authData === false) {
            $response = $this->systemLogin();
            if (!empty($response['data']['userId']) && !empty($response['data']['authToken'])) {
                $authData = [
                    'userId' => $response['data']['userId'],
                    'authToken' => $response['data']['authToken']
                ];
                $duration = 3600 * 24 * 30;
                $cache->set($key, $authData, $duration);
            } else {
                Yii::error(VarDumper::dumpAsString(['message' => 'Error: empty response[data][userId] or response[data][authToken]', 'response' => $response, 'url' => $this->url, 'username' => $this->username]), 'RocketChat:getSystemAuthData:systemLogin');
            }
        }

        if (empty($authData['userId'])) {
            $authData['userId'] = null;
        }

        if (empty($authData['authToken'])) {
            $authData['authToken'] = null;
        }

        return is_array($authData) ? $authData : [];
    }


    /**
     * @return mixed|null
     */
    public function getSystemUserId()
    {
        if (!empty($this->systemAuthData['userId'])) {
            return $this->systemAuthData['userId'];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getSystemAuthToken()
    {
        if (!empty($this->systemAuthData['authToken'])) {
            return $this->systemAuthData['authToken'];
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
     * @param bool $cacheEnable
     * @return array
     * @throws Exception
     */
    public function updateSystemAuth(bool $cacheEnable = true): array
    {
        $this->systemAuthData = $this->getSystemAuthData($cacheEnable);
        return $this->systemAuthData;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSystemAuthDataHeader(): array
    {
        $this->updateSystemAuth();

        return [
            'X-User-Id' => $this->systemAuthData['userId'],
            'X-Auth-Token' => $this->systemAuthData['authToken']
        ];
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
     * @throws Exception
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
        $headers = $this->getSystemAuthDataHeader();

        //return $headers;

        //VarDumper::dump($headers);

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
     * @param string|null $userRcId
     * @param string|null $username
     * @param bool $deleteByUsername
     * @return array
     * @throws Exception
     */
    public function deleteUser(?string $userRcId, ?string $username = null, bool $deleteByUsername = false): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        if ($userRcId && !$deleteByUsername) {
            $data['userId'] = $userRcId;
        } elseif ($username) {
            $data['username'] = $username;
        }

        $response = $this->sendRequest('users.delete', $data, 'post', $headers);

        if ($response->isOk) {
            //VarDumper::dump($response->data, 10, true); exit;

            if (empty($response->data['success'])) {
                $out['error'] = 'Success => false';
            } else {
                $out['data'] = $response->data;
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:deleteUser');
        }

        return $out;
    }


    /**
     * @param string $userId
     * @return array
     * @throws Exception
     */
    public function deleteUserByUserId(string $userId): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        $data['userId'] = $userId;

        $response = $this->sendRequest('users.delete', $data, 'post', $headers);

        if ($response->isOk) {
            //VarDumper::dump($response->data, 10, true); exit;

            if (empty($response->data['success'])) {
                $out['error'] = 'Success => false';
            } else {
                $out['data'] = $response->data;
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:deleteUserByUserId');
        }

        return $out;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAllDepartments(): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

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
     * @param array $message
     * @return array
     * @throws Exception
     */
    public function sendMessage(array $message): array
    {
        $out = ['error' => false, 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        $response = $this->sendRequest('chat.sendMessage', $message, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data['departments'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key [departments]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'RocketChat:sendMessage');
        }

        return $out;
    }

    public function me(string $userId, string $token): array
    {
        $out = ['error' => false, 'data' => []];

        $response = $this->sendRequest('me', [], 'get', [
            'X-User-Id' => $userId,
            'X-Auth-Token' => $token,
        ]);

        if ($response->isOk) {
            $out['data'] = $response->data;
        } else {
            $out['error'] = self::getErrorMessageFromResult($response->content);
        }

        return $out;
    }

    /**
     * @param string $url
     * @throws \Exception
     */
    public function downloadFile(string $url)
    {
        $out = ['error' => false, 'data' => []];
        $request = $this->getNewRequest();
        if ($request == null) {
            throw new \Exception("unable to create rocket chat request");
        }
        $request->setMethod("get")->setUrl($this->url . $url);
        $headers =  $this->getSystemAuthDataHeader();
        $request->setHeaders($headers);
        $response = $request->send();
        if ($response->isOk) {
            $out['data'] = $response->getContent();
        } else {
            $out['data'] = $response->content;
            $out['error'] = true;
            \Yii::error(VarDumper::dumpAsString($response, 10), 'RocketChat');
        }
        return $out;
    }

    /**
     * @param string $userId
     * @param array $data
     * @return array
     * @throws Exception
     * * RocketChat docs https://docs.rocket.chat/api/rest-api/methods/users/update
     */
    public function updateUser(string $userId, array $data): array
    {
        $out = ['error' => '', 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        $dataRequest = [
            'userId' => $userId,
            'data' => $data,
        ];

        $response = $this->sendRequest('users.update', $dataRequest, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data['user'])) {
                $out['data'] = $response->data['user'];
            } else {
                $out['error'] = 'Not found in response array data key [user]';
            }
        } else {
            $out['error'] = $response->content;
        }

        if (!empty($out['error'])) {
            \Yii::error(VarDumper::dumpAsString([
                'RequestData' => $dataRequest,
                'ResponseError' => $out['error'],
            ], 10), 'RocketChat:updateUser:fail');
        }

        return $out;
    }

    public function getDepartments()
    {
        $out = ['error' => '', 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        $response = $this->sendRequest('livechat/department', [], 'get', $headers);

        if ($response->isOk) {
            if (!empty($response->data['success'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key';
            }
        } else {
            $out['error'] = self::getErrorMessageFromResult($response->content);
        }

        if (!empty($out['error'])) {
            \Yii::error(
                VarDumper::dumpAsString($out['error'], 10),
                'RocketChat:getDepartments:fail'
            );
        }

        return $out;
    }

    public function createDepartment(array $data, string $rcAgentId, string $rcUsername): array
    {
        $out = ['error' => '', 'data' => []];
        $headers = $this->getSystemAuthDataHeader();

        $defaultData = [
            'department' => [
                'enabled' => true,
                'email' => '',
                'description' => '',
                'name' => '',
                'showOnRegistration' => true,
                'showOnOfflineForm' => false
            ],
            'agents' => [
                [
                    'agentId' => $rcAgentId,
                    'username' => $rcUsername
                ]
            ]
        ];
        $data = ArrayHelper::merge($defaultData, $data);
        $response = $this->sendRequest('livechat/department', $data, 'post', $headers);

        if ($response->isOk) {
            if (!empty($response->data['success'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key';
            }
        } else {
            $out['error'] = self::getErrorMessageFromResult($response->content);
        }

        if (!empty($out['error'])) {
            \Yii::error(
                VarDumper::dumpAsString($out['error'], 10),
                'RocketChat:getDepartments:fail'
            );
        }

        return $out;
    }

    public function removeDepartment(string $departmentId): array
    {
        $out = ['error' => '', 'data' => []];
        $headers = $this->getSystemAuthDataHeader();
        $response = $this->sendRequest('livechat/department/' . $departmentId, [], 'delete', $headers);

        if ($response->isOk) {
            if (!empty($response->data['success'])) {
                $out['data'] = $response->data;
            } else {
                $out['error'] = 'Not found in response array data key';
            }
        } else {
            $out['error'] = self::getErrorMessageFromResult($response->content);
        }

        if (!empty($out['error'])) {
            \Yii::error(
                VarDumper::dumpAsString($out['error'], 10),
                'RocketChat:removeDepartment:fail'
            );
        }

        return $out;
    }

    /**
     * @param int $length
     * @return string
     * @throws \yii\base\Exception
     */
    public static function generatePassword(int $length = 20): string
    {
        return Yii::$app->security->generateRandomString(20);
    }

    /**
     * @param int $days
     * @return false|string
     */
    public static function generateTokenExpired(int $days = 60)
    {
        return date('Y-m-d H:i:s', strtotime('+' . $days . ' days'));
    }

    /**
     * @param $result
     * @return string
     * @throws \JsonException
     */
    public static function getErrorMessageFromResult($result): string
    {
        $errorMessage = 'Unknown error message';
        if (!empty($result['error'])) {
            if (JsonHelper::isValidJson($result['error'])) {
                $errorArr = @json_decode($result['error'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($errorArr['message'])) {
                    $errorMessage = $errorArr['message'];
                } elseif (isset($errorArr['error'])) {
                    $errorMessage = $errorArr['error'];
                } else {
                    $errorMessage = VarDumper::dumpAsString($result['error']);
                }
            } else {
                $errorMessage = VarDumper::dumpAsString($result['error']);
            }
        } elseif (isset($result['success']) && !$result['success']) {
            $errorMessage = $result['message'] ?? 'Unknown error message';
        } elseif (!is_array($result)) {
            $error = Json::decode($result);
            if (isset($error['success']) && !$error['success']) {
                $errorMessage = $error['message'] ?? 'Unknown error message';
            }
            if (isset($error['status']) && $error['status'] === 'error') {
                $errorMessage = $error['message'] ?? 'Unknown error message';
            }
        } else {
            $errorMessage = VarDumper::dumpAsString($result);
        }
        return $errorMessage;
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        $result = $this->me($this->currentUserId, $this->currentAuthToken);
        return isset($result['data']);
    }
}
