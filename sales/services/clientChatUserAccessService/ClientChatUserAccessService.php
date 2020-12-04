<?php

namespace sales\services\clientChatUserAccessService;

use common\components\purifier\Purifier;
use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\ResetChatUserAccessWidgetEvent;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatUserAccessService
 * @package sales\services\clientChatUserAccessService
 *
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 * @property ClientChatUserChannelRepository $clientChatUserChannelRepository
 * @property TransactionManager $transactionManager
 */
class ClientChatUserAccessService
{
    /**
     * @var ClientChatUserAccessRepository
     */
    private ClientChatUserAccessRepository $clientChatUserAccessRepository;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ClientChatVisitorRepository
     */
    private ClientChatVisitorRepository $clientChatVisitorRepository;
    /**
     * @var ClientChatUserChannelRepository
     */
    private ClientChatUserChannelRepository $clientChatUserChannelRepository;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    public function __construct(
        ClientChatUserAccessRepository $clientChatUserAccessRepository,
        ClientChatRepository $clientChatRepository,
        ClientChatService $clientChatService,
        ClientChatVisitorRepository $clientChatVisitorRepository,
        ClientChatUserChannelRepository $clientChatUserChannelRepository,
        TransactionManager $transactionManager
    ) {
        $this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatService = $clientChatService;
        $this->clientChatVisitorRepository = $clientChatVisitorRepository;
        $this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
        $this->transactionManager = $transactionManager;
    }

    public function acceptTransfer(ClientChat $chat, ClientChatUserAccess $access, int $status): void
    {
        $access->setStatus($status);
        $this->clientChatService->finishTransfer($chat, $access);
        $this->disableAccessForOtherUsersBatch($chat->cch_id, $access->ccua_user_id);
        $this->clientChatUserAccessRepository->save($access, $chat);
    }

    public function skipTransfer(ClientChat $chat, ClientChatUserAccess $access, int $status): void
    {
        $access->setStatus($status);
        $userAccesses = ClientChatUserAccess::find()->byChatId($chat->cch_id)->exceptById($access->ccua_id)->pending()->exists();
        if (!$userAccesses) {
            $this->clientChatService->cancelTransfer($chat, null, ClientChatStatusLog::ACTION_CANCEL_TRANSFER_BY_SYSTEM);
        }
        $this->clientChatUserAccessRepository->save($access, $chat);
    }

    public function acceptPending(ClientChat $chat, ClientChatUserAccess $access, int $status): void
    {
        $access->setStatus($status);
        $this->clientChatService->acceptChat($chat, $access->ccua_user_id);
        $this->disableAccessForOtherUsersBatch($chat->cch_id, $access->ccua_user_id);
        $this->clientChatUserAccessRepository->save($access, $chat);
    }

    public function skipPending(ClientChat $chat, ClientChatUserAccess $access, int $status): void
    {
        $access->setStatus($status);
        $this->clientChatUserAccessRepository->save($access, $chat);
    }

    public function takeIdle(ClientChat $chat, ClientChatUserAccess $access, int $status, Employee $owner): void
    {
        $access->setStatus($status);
        $takeClientChat = $this->clientChatService->takeClientChat($chat, $owner);
        $this->clientChatUserAccessRepository->save($access, $takeClientChat);

        $clientChatLink = Purifier::createChatShortLink($chat);
        Notifications::createAndPublish(
            $chat->cch_owner_user_id,
            'Chat was taken',
            'Client Chat was taken by ' . $takeClientChat->cchOwnerUser->nickname . ' (' . $clientChatLink . ')',
            Notifications::TYPE_INFO,
            true
        );
    }

    public function disableAccessForOtherUsersBatch(int $chatId, int $ownerId): bool
    {
        if (
            $query = (bool) ClientChatUserAccess::updateAll(
                ['ccua_status_id' => ClientChatUserAccess::STATUS_CANCELED],
                new Expression(
                    'ccua_cch_id = :chatId and ccua_user_id <> :userId and ccua_status_id = :status',
                    [
                    'chatId' => $chatId,
                    'userId' => $ownerId,
                    'status' => ClientChatUserAccess::STATUS_PENDING
                    ]
                )
            )
        ) {
            $users = ClientChatUserAccess::find()->select(['ccua_user_id', 'ccua_id'])->where(
                new Expression(
                    'ccua_cch_id = :chatId and ccua_user_id <> :userId and ccua_status_id = :status',
                    ['chatId' => $chatId, 'userId' => $ownerId, 'status' => ClientChatUserAccess::STATUS_PENDING]
                )
            )->asArray()->all();

            foreach ($users as $user) {
                $this->clientChatUserAccessRepository->updateChatUserAccessWidget(
                    $chatId,
                    (int) $user['ccua_user_id'],
                    ClientChatUserAccess::STATUS_CANCELED,
                    (int) $user['ccua_id']
                );
            }
            return true;
        }
        return false;
    }

    /**
     * @param int $userId
     * @throws \Throwable
     */
    public function disableUserAccessToAllChats(int $userId): void
    {
        $this->transactionManager->wrap(static function () use ($userId) {
            /** @var ClientChatUserAccess[] $userAccess  */
            $eventDispatcher = \Yii::createObject(EventDispatcher::class);
            $userAccess = ClientChatUserAccess::find()->select(['ccua_cch_id'])->byUserId($userId)->pending()->asArray()->all();
            $chats = ArrayHelper::getColumn($userAccess, 'ccua_cch_id');
            ClientChatUserAccess::updateAll(['ccua_status_id' => ClientChatUserAccess::STATUS_CANCELED], ['ccua_cch_id' => $chats, 'ccua_user_id' => $userId, 'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING]);
            $eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
        });
    }

    /**
     * @param ClientChatUserChannel[] $userChannels
     * @throws \Throwable
     */
    public function setUserAccessToAllChats(array $userChannels): void
    {
        $_self = $this;
        $this->transactionManager->wrap(static function () use ($userChannels, $_self) {
            foreach ($userChannels as $userChannel) {
                if ($chats = ClientChat::find()->byOwner(null)->byChannel($userChannel->ccuc_channel_id)->all()) {
                    foreach ($chats as $chat) {
                        $_self->clientChatService->sendRequestToUser($chat, $userChannel);
                    }
                }
            }
        });
    }

    public function setUserAccessToAllChatsByChannelIds(array $channelIds, int $userId)
    {
        $eventDispatcher = \Yii::createObject(EventDispatcher::class);
        $chatIds = ClientChatUserAccess::find()->select(['ccua_cch_id'])->byUserId($userId)->pending()->column();
        if ($chats = ClientChat::find()->select(['cch_id'])->conditionSetUserAccess()->byChannelIds($channelIds)->excludeChatIds($chatIds)->asArray()->all()) {
            $data = [];
            foreach ($chats as $chat) {
                $data[] = [
                    'ccua_cch_id' => $chat['cch_id'],
                    'ccua_user_id' => $userId,
                    'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING,
                    'ccua_created_dt' => date('Y-m-d H:i:s'),
                    'ccua_updated_dt' => date('Y-m-d H:i:s'),
                ];
            }
            \Yii::$app->db->createCommand()->batchInsert(ClientChatUserAccess::tableName(), ['ccua_cch_id', 'ccua_user_id', 'ccua_status_id', 'ccua_created_dt', 'ccua_updated_dt'], $data)->execute();
        }
        $eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
    }

    public function disableUserAccessToAllChatsByChannelIds(array $channelIds, int $userId): void
    {
        $eventDispatcher = \Yii::createObject(EventDispatcher::class);
        if ($chats = ClientChat::find()->select(['cch_id'])->byOwner(null)->byChannelIds($channelIds)->asArray()->all()) {
            $chats = ArrayHelper::getColumn($chats, 'cch_id');
            ClientChatUserAccess::updateAll(['ccua_status_id' => ClientChatUserAccess::STATUS_CANCELED], ['ccua_cch_id' => $chats, 'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING, 'ccua_user_id' => $userId]);
        }
        $eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
    }
}
