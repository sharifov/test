<?php

namespace sales\services\clientChatMessage;

use common\models\Notifications;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatUnread\entity\ClientChatUnreadRepository;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use sales\repositories\call\CallRepository;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatMessageService
 * @package sales\services\clientChatMessage
 *
 * @property CallRepository $callRepository
 * @property ClientChatUnreadRepository $unreadRepository
 * @property ClientChatMessageRepository $clientChatMessageRepository
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
     * @var ClientChatMessageRepository
     */
    private ClientChatMessageRepository $clientChatMessageRepository;

    public function __construct(
        CallRepository $callRepository,
        ClientChatUnreadRepository $unreadRepository,
        ClientChatMessageRepository $clientChatMessageRepository
    ) {
        $this->callRepository = $callRepository;
        $this->unreadRepository = $unreadRepository;
        $this->clientChatMessageRepository = $clientChatMessageRepository;
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

    /**
     * @param string $rid
     * @return ClientChatMessage[]
     */
    public function getFreeMessages(string $rid): array
    {
        return ClientChatMessage::find()->andWhere(['ccm_rid' => $rid])->andWhere(['is', 'ccm_cch_id', null])->all();
    }

    public function assignMessagesToChat(ClientChat $chat): void
    {
        $messages = $this->getFreeMessages($chat->cch_rid);
        foreach ($messages as $message) {
            $this->assignMessageToChat($message, $chat);
        }
    }

    public function assignMessageToChat(ClientChatMessage $message, ClientChat $clientChat): void
    {
        $ownerUserId = null;
        if ($message->isAgentUttered()) {
            $ownerUserId = $clientChat->cch_owner_user_id;
        }
        $message->assignToChat($clientChat->cch_id, $clientChat->cch_client_id, $ownerUserId);
        $this->clientChatMessageRepository->save($message, 0);

        $this->sendLastChatMessageToMonitor($message);

        if ($message->isGuestUttered()) {
            if ($clientChat->hasOwner() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userClientChatData->isRegisteredInRc()) {
                if (!UserConnectionActiveChat::find()->andWhere(['ucac_chat_id' => $clientChat->cch_id])->exists()) {
                    $countUnreadByChatMessages = $this->increaseUnreadMessages($clientChat->cch_id);
                    $this->updateMessageInfoNotification($countUnreadByChatMessages, $clientChat, $message);
                } else {
                    $this->touchUnreadMessage($clientChat->cch_id);
                    Notifications::publish('clientChatUpdateItemInfo', ['user_id' => $clientChat->cch_owner_user_id], [
                        'data' => [
                            'cchId' => $clientChat->cch_id,
                            'shortMessage' => StringHelper::truncate($message->getMessage(), 40, '...'),
                            'messageOwner' => $message->isMessageFromClient() ? 'client' : 'agent',
                            'moment' => round((time() - strtotime($message->ccm_sent_dt))),
                        ]
                    ]);
                }
            } else {
                $this->increaseUnreadMessages($clientChat->cch_id);
            }
            (Yii::createObject(ClientChatLastMessageRepository::class))->createOrUpdateByMessage($message);
        } elseif ($message->isAgentUttered()) {
            $this->touchUnreadMessage($clientChat->cch_id);
            if ($clientChat->hasOwner() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userClientChatData->isRegisteredInRc()) {
                Notifications::publish('clientChatUpdateItemInfo', ['user_id' => $clientChat->cch_owner_user_id], [
                    'data' => [
                        'cchId' => $clientChat->cch_id,
                        'shortMessage' => StringHelper::truncate($message->getMessage(), 40, '...'),
                        'messageOwner' => $message->isMessageFromClient() ? 'client' : 'agent',
                        'moment' => round((time() - strtotime($message->ccm_sent_dt))),
                    ]
                ]);
            }
            (Yii::createObject(ClientChatLastMessageRepository::class))->createOrUpdateByMessage($message);
        }
    }

    public function sendLastChatMessageToMonitor(ClientChatMessage $message): void
    {
        $data = [];
        $data['chat_id'] = $message->ccm_cch_id;
        $data['client_id'] = $message->ccm_client_id;
        $data['user_id'] = $message->ccm_user_id;
        $data['sent_dt'] = Yii::$app->formatter->asDatetime(strtotime($message->ccm_sent_dt), 'php: Y-m-d H:i:s');
        $data['period'] = Yii::$app->formatter->asRelativeTime(strtotime($message->ccm_sent_dt));
        $data['msg'] = $message->message;

        try {
            Yii::$app->centrifugo->setSafety(false)->publish('realtimeClientChatChannel', ['message' => json_encode([
                'chatMessageData' => $data,
            ])]);
        } catch (\Throwable $throwable) {
            Yii::error(
                VarDumper::dumpAsString($throwable),
                'ClientChatRequestService:sendLastChatMessageToMonitor'
            );
        }
    }

    private function updateMessageInfoNotification($countUnreadByChatMessages, ClientChat $clientChat, ClientChatMessage $message): void
    {
        Notifications::publish('clientChatUnreadMessage', ['user_id' => $clientChat->cch_owner_user_id], [
            'data' => [
                'cchId' => $clientChat->cch_id,
                'totalUnreadMessages' => $this->getCountOfTotalUnreadMessagesByUser($clientChat->cch_owner_user_id) ?: '',
                'cchUnreadMessages' => $countUnreadByChatMessages,
                'soundNotification' => $this->soundNotification($clientChat->cch_owner_user_id),
                'shortMessage' => StringHelper::truncate($message->getMessage(), 40, '...'),
                'messageOwner' => $message->isMessageFromClient() ? 'client' : 'agent',
                'moment' =>  round((time() - strtotime($message->ccm_sent_dt))),
            ]
        ]);
    }
}
