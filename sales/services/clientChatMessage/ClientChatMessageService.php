<?php

namespace sales\services\clientChatMessage;

use common\models\Notifications;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatUnread\entity\ClientChatUnreadRepository;
use sales\repositories\call\CallRepository;
use yii\db\ActiveQuery;

/**
 * Class ClientChatMessageService
 * @package sales\services\clientChatMessage
 *
 * @property CallRepository $callRepository
 * @property ClientChatUnreadRepository $unreadRepository
 */
class ClientChatMessageService
{
    /**
     * @var CallRepository
     */
    private CallRepository $callRepository;
    /**
     * @var ClientChatUnreadRepository
     */
    private ClientChatUnreadRepository $unreadRepository;

    /**
     * ClientChatMessageService constructor.
     * @param CallRepository $callRepository
     * @param ClientChatUnreadRepository $unreadRepository
     */
    public function __construct(CallRepository $callRepository, ClientChatUnreadRepository $unreadRepository)
    {
        $this->callRepository = $callRepository;
        $this->unreadRepository = $unreadRepository;
    }

    public function increaseUnreadMessages(int $chatId): int
    {
        $unread = $this->unreadRepository->get($chatId);
        if (!$unread) {
            $unread = ClientChatUnread::create($chatId, 0, new \DateTimeImmutable());
        }
        $unread->increase(new \DateTimeImmutable());
        try {
            $this->unreadRepository->save($unread);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client chat message increase error',
                'model' => $unread->getAttributes(),
                'errors' => $e->getMessage(),
            ], 'ClientChatMessageService:increaseUnreadMessages');
        }

        return $unread->ccu_count;
    }

    public function touchUnreadMessage(int $chatId): void
    {
        $unread = $this->unreadRepository->get($chatId);
        if (!$unread) {
            $unread = ClientChatUnread::create($chatId, 0, new \DateTimeImmutable());
        }
        $unread->touch(new \DateTimeImmutable());
        try {
            $this->unreadRepository->save($unread);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client chat message increase error',
                'model' => $unread->getAttributes(),
                'errors' => $e->getMessage(),
            ], 'ClientChatMessageService:touchUnreadMessageDate');
        }
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
        $unread = $this->unreadRepository->get($chatId);
        if (!$unread) {
            $unread = ClientChatUnread::create($chatId, 0, new \DateTimeImmutable());
        }

        $unread->resetCounter();
        try {
            $this->unreadRepository->save($unread);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Client chat discard unread messages',
                'model' => $unread->getAttributes(),
                'errors' => $e->getErrors(),
            ], 'ClientChatMessageService:discardUnreadMessages');
        }

        if (!$userId) {
            return;
        }

        Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($userId) ?: '', 'cchId' => $chatId, 'cchUnreadMessages' => null]]);
    }

    public function discardAllUnreadMessagesForUser(int $userId): void
    {
        $unreadMessages = ClientChatUnread::find()->innerJoinWith(['chat' => static function (ActiveQuery $query) use ($userId) {
            return $query->andOnCondition(['cch_owner_user_id' => $userId]);
        }], false)->all();

        foreach ($unreadMessages as $unreadMessage) {
            try {
                $this->unreadRepository->remove($unreadMessage);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client chat discard all unread messages for user',
                    'model' => $unreadMessage->getAttributes(),
                    'errors' => $e->getMessage(),
                ], 'ClientChatMessageService:discardAllUnreadMessagesForUser');
            }
        }

        Notifications::publish('clientChatUnreadMessage', ['user_id' => $userId], ['data' => ['totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($userId) ?: '', 'refreshPage' => 1]]);
    }

    public function soundNotification(int $userId): bool
    {
        return SettingHelper::isCcSoundNotificationEnabled() && !$this->callRepository->isUserHasActiveCalls($userId);
    }
}
