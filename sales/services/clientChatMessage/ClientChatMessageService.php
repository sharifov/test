<?php
namespace sales\services\clientChatMessage;

use common\models\Notifications;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\repositories\call\CallRepository;
use yii\db\ActiveQuery;

/**
 * Class ClientChatMessageService
 * @package sales\services\clientChatMessage
 *
 * @property CallRepository $callRepository
 */
class ClientChatMessageService
{
	/**
	 * @var CallRepository
	 */
	private CallRepository $callRepository;

	/**
	 * ClientChatMessageService constructor.
	 * @param CallRepository $callRepository
	 */
	public function __construct(CallRepository $callRepository)
	{
		$this->callRepository = $callRepository;
	}

	public function increaseUnreadMessages(int $cchId, int $userId): self
	{
		$unread = ClientChatUnread::find()->andWhere(['ccu_cc_id' => $cchId])->one();
		if (!$unread) {
		    $unread = new ClientChatUnread();
		    $unread->ccu_cc_id = $cchId;
        }
		$unread->increase();
		try {
            if (!$unread->save()) {
                \Yii::error([
                    'message' => 'Client chat message increase error',
                    'model' => $unread->getErrors(),
                    'errors' => $unread->getErrors(),
                ], 'ClientChatMessageService:increaseUnreadMessages');
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client chat message increase error',
                'model' => $unread->getErrors(),
                'errors' => $e->getMessage(),
            ], 'ClientChatMessageService:increaseUnreadMessages');
        }

		Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($userId) ?: '', 'cchId' => $cchId, 'cchUnreadMessages' => $unread->ccu_count, 'soundNotification' => $this->soundNotification($userId)]]);
		return $this;
	}

    public function getUserChatsIdWithUnreadMessages(int $userId): array
    {
        return array_keys(ClientChat::find()->select(['cch_id'])->byOwner($userId)->withUnreadMessage()->indexBy('cch_id')->column());
	}

	public function getCountOfChatUnreadMessagesByUser(int $cchId, int $userId): int
	{
        $totalUnreadMessages = ClientChat::find()->select(['ccu_count as count'])->byId($cchId)->byOwner($userId)->withUnreadMessage()->asArray()->one();
        return $totalUnreadMessages ? (int)$totalUnreadMessages['count'] : 0;
	}

	public function getCountOfTotalUnreadMessagesByUser(int $userId): int
	{
        $totalUnreadMessages = ClientChat::find()->select(['sum(ccu_count) as count'])->byOwner($userId)->withUnreadMessage()->asArray()->one();
        return $totalUnreadMessages ? (int)$totalUnreadMessages['count'] : 0;
	}

	public function discardUnreadMessages(int $chatId, ?int $userId): void
	{
        $unreadMessage = ClientChatUnread::find()->andWhere(['ccu_cc_id' => $chatId])->one();

        if (!$unreadMessage) {
            return;
        }

        try {
            if (!$unreadMessage->delete()) {
                \Yii::error([
                    'message' => 'Client chat discard unread messages',
                    'model' => $unreadMessage->getErrors(),
                    'errors' => $unreadMessage->getErrors(),
                ], 'ClientChatMessageService:discardUnreadMessages');
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client chat discard unread messages',
                'model' => $unreadMessage->getErrors(),
                'errors' => $e->getMessage(),
            ], 'ClientChatMessageService:discardUnreadMessages');
        }

        if (!$userId) {
            return;
        }

        Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($userId) ?: '', 'cchId' => $chatId, 'cchUnreadMessages' => null]]);
	}

	public function discardAllUnreadMessagesForUser(int $userId): void
	{
        $unreadMessages = ClientChatUnread::find()->innerJoinWith(['chat' => static function(ActiveQuery $query) use ($userId) {
            return $query->andOnCondition(['cch_owner_user_id' => $userId]);
        }], false)->all();

        foreach ($unreadMessages as $unreadMessage) {
            try {
                if (!$unreadMessage->delete()) {
                    \Yii::error([
                        'message' => 'Client chat discard all unread messages for user',
                        'model' => $unreadMessage->getErrors(),
                        'errors' => $unreadMessage->getErrors(),
                    ], 'ClientChatMessageService:discardAllUnreadMessagesForUser');
                }
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client chat discard all unread messages for user',
                    'model' => $unreadMessage->getErrors(),
                    'errors' => $e->getMessage(),
                ], 'ClientChatMessageService:discardAllUnreadMessagesForUser');
            }
        }

		Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($userId) ?: '', 'refreshPage' => 1]]);
	}

	private function soundNotification(int $userId): bool
	{
		return SettingHelper::isCcSoundNotificationEnabled() && !$this->callRepository->isUserHasActiveCalls($userId);
	}
}