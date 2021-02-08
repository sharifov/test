<?php

namespace sales\services\clientChat;

use common\models\Employee;
use sales\model\userClientChatData\service\UserClientChatDataService;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Exception;

/**
 * Class ClientChatRegistration
 */
class ClientChatRequesterService
{
    /**
     * @param string $username
     * @param string $name
     * @param string $email
     * @param string $password
     * @param bool $active
     * @param array $roles
     * @return array [rcUserId, authToken]
     * @throws Exception
     * @throws \JsonException
     */
    public static function register(
        string $username,
        string $name,
        string $email,
        string $password,
        bool $active = true,
        array $roles = ["user", "livechat-agent"]
    ): array {
        $rocketChat = \Yii::$app->rchat;
        $rocketChat->updateSystemAuth(false);

        $createRequest = $rocketChat->createUser(
            $username,
            $password,
            $name,
            $email,
            $roles,
            $active
        );

        if (isset($createRequest['error']) && !$createRequest['error']) {
            if (empty($createRequest['data']['_id'])) {
                throw new \RuntimeException(
                    'Empty result[data][_id]. ' .
                    VarDumper::dumpAsString(['data' => $createRequest]),
                    -1
                );
            }
        } else {
            $errorMessage = $rocketChat::getErrorMessageFromResult($createRequest);
            throw new \RuntimeException('Error from RocketChat. ' . $errorMessage, -1);
        }

        $loginRequest = $rocketChat->login($username, $password);

        if (isset($loginRequest['error']) && $loginRequest['error']) {
            throw new \RuntimeException(VarDumper::dumpAsString($loginRequest['error']), -1);
        }
        if (empty($loginRequest['data']['authToken'])) {
            throw new \RuntimeException(
                'Empty authToken. ' .
                VarDumper::dumpAsString(['data' => $loginRequest]),
                -1
            );
        }
        return [
            'rcUserId' => $createRequest['data']['_id'],
            'authToken' => $loginRequest['data']['authToken']
        ];
    }

    public static function checkRegisterEmployee(int $employeeId): Employee
    {
        if (!$user = Employee::findOne($employeeId)) {
            throw new \RuntimeException('Employee not found. Id: ' . $employeeId, -1);
        }
        if ($userChatData = UserClientChatDataService::getUserChatData($user)) {
            throw new \RuntimeException('Employee already registered. Rc user id: ' . $userChatData->getRcUserId(), -1);
        }
        return $user;
    }

    /**
     * @param $username
     * @param $password
     * @return string
     * @throws Exception
     */
    public static function refreshToken($username, $password): string
    {
        $result = \Yii::$app->rchat->login($username, $password);

        if ($result['error'] !== false) {
            if ($result['error'] === 'You must be logged in to do this.') {
                throw new \RuntimeException('Invalid credential');
            }
            throw new \RuntimeException((string)$result['error']);
        }

        if (empty($result['data']['authToken'])) {
            throw new \RuntimeException('index authToken not found in rocket chat api response');
        }
        return $result['data']['authToken'];
    }
}
