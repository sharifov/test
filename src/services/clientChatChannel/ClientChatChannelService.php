<?php

namespace src\services\clientChatChannel;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Exception;

/**
 * Class ClientChatChannelService
 * @package src\services\clientChatChannel
 *
 * @property array $rocketChatDepartments
 * @property array $rocketChatUserInfo
 * @property string $username
 */
class ClientChatChannelService
{
    private array $rocketChatDepartments = [];

    private array $rocketChatUserInfo = [];

    private string $username = '';

    /**
     * @param int $channelId
     * @param string $username
     * @return void
     * @throws Exception
     */
    public function registerChannelInRocketChat(int $channelId, string $username): void
    {
        if ($this->username !== $username) {
            $userInfo = \Yii::$app->chatBot->getUserInfo($username);
            if (isset($userInfo['error']['error'])) {
                throw new \RuntimeException('[Chat Bot User Info] ' . $userInfo['error']['error'] . '; User: ' . $username, ClientChatChannelCodeException::RC_USER_INFO);
            }

            if (isset($userInfo['error']['status'], $userInfo['error']['message']) && $userInfo['error']['status'] === 'error') {
                throw new \RuntimeException('[Chat Bot User Info] ' . $userInfo['error']['message'] . '; User: ' . $username, ClientChatChannelCodeException::RC_USER_INFO);
            }

            if (!isset($userInfo['data']['user'])) {
                throw new \RuntimeException('[Chat Bot User Info] not found user data in response; User: ' . $username, ClientChatChannelCodeException::RC_USER_NOT_FOUND);
            }
            $this->rocketChatUserInfo = $userInfo['data']['user'];
            $this->username = $username;
        }

        if ($this->isExistChanelInRocketChatDepartments($channelId)) {
            throw new \RuntimeException('Channel with id: ' . $channelId . ' already exist in rocket chat', ClientChatChannelCodeException::RC_DEPARTMENT_EXIST);
        }

        $newDepartmentData = [
            'department' => [
                'name' => (string)$channelId
            ],
        ];

        \Yii::$app->rchat->updateSystemAuth(false);
        $newDepartment = \Yii::$app->rchat->createDepartment($newDepartmentData, $this->rocketChatUserInfo['_id'] ?? '', $this->rocketChatUserInfo['username'] ?? '');
        if ($newDepartment['error']) {
            throw new \RuntimeException('[Chat Bot Create Department] ' . $newDepartment['error'] . '; ChannelId: ' . $channelId, ClientChatChannelCodeException::RC_CREATE_DEPARTMENT);
        }
    }

    public function unRegisterChannelInRocketChat(int $channelId): void
    {
        if (!$this->isExistChanelInRocketChatDepartments($channelId)) {
            throw new \RuntimeException('Channel with id: ' . $channelId . ' already unregistered', ClientChatChannelCodeException::RC_REMOVE_DEPARTMENT);
        }

        if (!$rocketChatDepartmentId = $this->getRocketChatDepartmentId($channelId)) {
            throw new \RuntimeException('[Chat Bot UnRegistered Department] not found rocket chat department Id', ClientChatChannelCodeException::RC_REMOVE_DEPARTMENT);
        }

        $result = \Yii::$app->rchat->removeDepartment($rocketChatDepartmentId);

        if ($result['error']) {
            throw new \RuntimeException('[Chat Bot UnRegistered Department] ' . $result['error'] . '; ChannelId: ' . $channelId, ClientChatChannelCodeException::RC_REMOVE_DEPARTMENT);
        }

        if (!isset($result['data']['success'])) {
            throw new \RuntimeException('[Chat Bot UnRegistered Department] not found success response; ChannelId: ' . $channelId, ClientChatChannelCodeException::RC_REMOVE_DEPARTMENT);
        }

        if (!$result['data']['success']) {
            throw new \RuntimeException('[Chat Bot UnRegistered Department] success response error; ChannelId: ' . $channelId, ClientChatChannelCodeException::RC_REMOVE_DEPARTMENT);
        }
    }

    public function validateChannelInRocketChat(int $channelId): bool
    {
        return $this->isExistChanelInRocketChatDepartments($channelId);
    }

    public function getRocketChatDepartments(): array
    {
        if ($this->rocketChatDepartments) {
            return $this->rocketChatDepartments;
        }
        $request = \Yii::$app->rchat->getDepartments();
        if ($request['data'] && !empty($request['data']['departments'])) {
            $this->rocketChatDepartments = $request['data']['departments'];
        }
        return $this->rocketChatDepartments;
    }

    private function getRocketChatDepartmentsNames(): array
    {
        return ArrayHelper::getColumn($this->getRocketChatDepartments(), 'name');
    }

    private function isExistChanelInRocketChatDepartments(int $channelId): bool
    {
        return in_array($channelId, $this->getRocketChatDepartmentsNames(), false);
    }

    private function getRocketChatDepartmentId(int $channelId): ?string
    {
        $departmentId = null;
        foreach ($this->getRocketChatDepartments() as $item) {
            if ($item['name'] === (string)$channelId) {
                $departmentId = $item['_id'];
            }
        }
        return $departmentId;
    }
}
