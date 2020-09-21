<?php


namespace sales\services\clientChatChannel;


use http\Exception\RuntimeException;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatChannelService
 * @package sales\services\clientChatChannel
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

		if (!$this->rocketChatDepartments) {
			$request = \Yii::$app->rchat->getDepartments();
			if ($request['data'] && $request['data']['departments']) {
				$this->rocketChatDepartments = ArrayHelper::getColumn($request['data']['departments'], 'name');
			}
		}

		if (!in_array($channelId, $this->rocketChatDepartments, false)) {
			$newDepartmentData = [
				'department' => [
					'name' => (string)$channelId
				],
			];

			$newDepartment = \Yii::$app->rchat->createDepartment($newDepartmentData, $this->rocketChatUserInfo['_id'] ?? '', $this->rocketChatUserInfo['username'] ?? '');
			if ($newDepartment['error']) {
				throw new \RuntimeException('[Chat Bot Create Department] '.$newDepartment['error'].'; ChannelId: ' . $channelId, ClientChatChannelCodeException::RC_CREATE_DEPARTMENT);
			}
		} else {
			throw new \RuntimeException('Channel with id: ' . $channelId . ' already exist in rocket chat', ClientChatChannelCodeException::RC_DEPARTMENT_EXIST);
		}
	}
}