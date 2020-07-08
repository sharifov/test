<?php
namespace sales\services\clientChatMessage;

use common\models\Notifications;
use frontend\widgets\notification\NotificationCache;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\redis\Connection;

/**
 * Class ClientChatMessageService
 * @package sales\services\clientChatMessage
 *
 * @property Connection $redis
 */
class ClientChatMessageService
{
	private Connection $redis;

	public function __construct()
	{
		$this->redis = \Yii::$app->redis;
	}

	public function increaseUnreadMessages(int $cchId, int $userId): self
	{
		$unreadMessages = $this->getCountOfChatUnreadMessages($cchId, $userId);
		$this->setUnreadMessages($cchId, $userId, ++$unreadMessages);
		Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessages($userId) ?: '']]);
		return $this;
	}

	public function getCountOfChatUnreadMessages(int $cchId, int $userId): int
	{
		return (int)$this->redis->get($this->unreadMessageChatKey($cchId, $userId));
	}

	public function getCountOfTotalUnreadMessages(int $userId): int
	{
		return (int)$this->redis->get($this->totalUnreadMessagesByUserKey($userId));
	}

	public function discardUnreadMessages(int $cchId, int $userId): void
	{
		$chatUnreadMessages = (int)$this->redis->get($this->unreadMessageChatKey($cchId, $userId));
		if ($chatUnreadMessages) {
			$total = (int)$this->redis->get($this->totalUnreadMessagesByUserKey($userId));
			$this->redis->del($this->unreadMessageChatKey($cchId, $userId));
			$this->redis->set($this->totalUnreadMessagesByUserKey($userId), $total - $chatUnreadMessages);
			$this->removeChatWithUnreadMessages($cchId, $userId);

			NotificationCache::invalidateCc($userId);

			Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessages($userId) ?: '']]);
		}
	}

	public function getChatWithUnreadMessages(int $userId): array
	{
		return Json::decode($this->redis->get($this->chatWithUnreadMessagesKey($userId))) ?? [];
	}

	private function setUnreadMessages(int $cchId, int $userId, int $count): void
	{
		$this->redis->set($this->unreadMessageChatKey($cchId, $userId), $count);
		$total = (int)$this->redis->get($this->totalUnreadMessagesByUserKey($userId));
		$this->redis->set($this->totalUnreadMessagesByUserKey($userId), ++$total);

		$this->setChatWithUnreadMessages($cchId, $userId);

		NotificationCache::invalidateCc($userId);
	}

	private function unreadMessageChatKey(int $cchId, int $userId): string
	{
		return '_chat_' . $cchId . '_user_' . $userId;
	}

	private function setChatWithUnreadMessages(int $cchId, int $userId): void
	{
		$chats = $this->getChatWithUnreadMessages($userId);
		if (!in_array($cchId, $chats, false)) {
			$chats[] = $cchId;
			$this->redis->set($this->chatWithUnreadMessagesKey($userId), Json::encode($chats));
		}
	}
	private function removeChatWithUnreadMessages(int $cchId, int $userId): void
	{
		$chats = $this->getChatWithUnreadMessages($userId);
		if (in_array($cchId, $chats, false)) {
			$chats = ArrayHelper::removeValue($chats, $cchId);
			$this->redis->set($this->chatWithUnreadMessagesKey($userId), Json::encode($chats));
		}
	}


	private function totalUnreadMessagesByUserKey(int $userId): string
	{
		return '_total_unread_messages_' . $userId;
	}

	private function chatWithUnreadMessagesKey(int $userId): string
	{
		return '_user_' . $userId . '_chat_unread_messages';
	}
}