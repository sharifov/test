<?php

namespace src\services\clientChatService;

use common\components\jobs\clientChat\ChatAssignUserAccessPendingChatsJob;
use common\components\jobs\clientChat\ClientChatUserAccessJob;
use common\components\purifier\Purifier;
use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Notifications;
use common\models\search\EmployeeSearch;
use common\models\UserProfile;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\notification\NotificationMessage;
use src\dispatchers\DeferredEventDispatcher;
use src\forms\clientChat\RealTimeStartChatForm;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientChat\ClientChatCodeException;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\entity\ClientChatQuery;
use src\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use src\model\clientChat\useCase\close\ClientChatCloseForm;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChat\useCase\transfer\ClientChatTransferForm;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatCase\entity\ClientChatCaseRepository;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatChannelTransfer\ClientChatChannelTransferRule;
use src\model\clientChatLastMessage\ClientChatLastMessageRepository;
use src\model\clientChatLastMessage\entity\ClientChatLastMessage;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\clientChatLead\entity\ClientChatLeadRepository;
use src\model\clientChatNote\ClientChatNoteRepository;
use src\model\clientChatNote\entity\ClientChatNote;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\repository\ClientChatRequestRepository;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\model\clientChatUnread\entity\ClientChatUnread;
use src\model\clientChatUnread\entity\ClientChatUnreadRepository;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use src\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use src\repositories\clientChatChannel\ClientChatChannelRepository;
use src\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use src\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use src\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use src\repositories\NotFoundException;
use src\repositories\visitorLog\VisitorLogRepository;
use src\services\client\ClientManageService;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;
use src\services\TransactionManager;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatService
 * @package src\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatRepository $clientChatRepository
 * @property TransactionManager $transactionManager
 * @property VisitorLogRepository $visitorLogRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 * @property ClientChatLeadRepository $clientChatLeadRepository
 * @property ClientChatCaseRepository $clientChatCaseRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatUserChannelRepository $clientChatUserChannelRepository
 * @property ClientChatNoteRepository $clientChatNoteRepository
 * @property ClientChatStatusLogRepository $clientChatStatusLogRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatUnreadRepository $clientChatUnreadRepository
 * @property ClientChatLastMessageRepository $clientChatLastMessageRepository
 */
class ClientChatService
{
    /**
     * @var ClientChatChannelRepository
     */
    private ClientChatChannelRepository $clientChatChannelRepository;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var VisitorLogRepository
     */
    private VisitorLogRepository $visitorLogRepository;
    /**
     * @var ClientChatUserAccessRepository
     */
    private ClientChatUserAccessRepository $clientChatUserAccessRepository;
    /**
     * @var ClientChatVisitorRepository
     */
    private ClientChatVisitorRepository $clientChatVisitorRepository;
    /**
     * @var ClientChatLeadRepository
     */
    private ClientChatLeadRepository $clientChatLeadRepository;
    /**
     * @var ClientChatCaseRepository
     */
    private ClientChatCaseRepository $clientChatCaseRepository;
    /**
     * @var ClientManageService
     */
    private ClientManageService $clientManageService;
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;
    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $clientChatRequestRepository;
    /**
     * @var ClientChatUserChannelRepository
     */
    private ClientChatUserChannelRepository $clientChatUserChannelRepository;
    /**
     * @var ClientChatNoteRepository
     */
    private ClientChatNoteRepository $clientChatNoteRepository;
    /**
     * @var ClientChatStatusLogRepository
     */
    private ClientChatStatusLogRepository $clientChatStatusLogRepository;

    private ClientChatMessageService $clientChatMessageService;

    private ClientChatUnreadRepository $clientChatUnreadRepository;
    private ClientChatLastMessageRepository $clientChatLastMessageRepository;

    private const REDIS_DISTRIBUTION_LOGIC_KEY = '-chat-distribution-logic';
    private const REDIS_DISTRIBUTION_LOGIC_EXPIRE_S = 1800;

    public function __construct(
        ClientChatChannelRepository $clientChatChannelRepository,
        ClientChatRepository $clientChatRepository,
        TransactionManager $transactionManager,
        VisitorLogRepository $visitorLogRepository,
        ClientChatUserAccessRepository $clientChatUserAccessRepository,
        ClientChatVisitorRepository $clientChatVisitorRepository,
        ClientChatLeadRepository $clientChatLeadRepository,
        ClientChatCaseRepository $clientChatCaseRepository,
        ClientManageService $clientManageService,
        ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
        ClientChatRequestRepository $clientChatRequestRepository,
        ClientChatUserChannelRepository $clientChatUserChannelRepository,
        ClientChatNoteRepository $clientChatNoteRepository,
        ClientChatStatusLogRepository $clientChatStatusLogRepository,
        ClientChatMessageService $clientChatMessageService,
        ClientChatUnreadRepository $clientChatUnreadRepository,
        ClientChatLastMessageRepository $clientChatLastMessageRepository
    ) {
        $this->clientChatChannelRepository = $clientChatChannelRepository;
        $this->clientChatRepository = $clientChatRepository;
        $this->transactionManager = $transactionManager;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
        $this->clientChatVisitorRepository = $clientChatVisitorRepository;
        $this->clientChatLeadRepository = $clientChatLeadRepository;
        $this->clientChatCaseRepository = $clientChatCaseRepository;
        $this->clientManageService = $clientManageService;
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
        $this->clientChatRequestRepository = $clientChatRequestRepository;
        $this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
        $this->clientChatNoteRepository = $clientChatNoteRepository;
        $this->clientChatStatusLogRepository = $clientChatStatusLogRepository;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatUnreadRepository = $clientChatUnreadRepository;
        $this->clientChatLastMessageRepository = $clientChatLastMessageRepository;
    }

    /**
     * @param ClientChat $clientChat
     * @param int $channelId
     * @return ClientChatChannel
     */
    public function assignClientChatChannel(ClientChat $clientChat, int $channelId): ClientChatChannel
    {
        try {
            $clientChatChannel = $this->clientChatChannelRepository->find($channelId);
        } catch (NotFoundException $e) {
            $clientChatChannel = $this->clientChatChannelRepository->findDefaultByProject((int)$clientChat->cch_project_id);
        }
        $clientChat->cch_channel_id = $clientChatChannel->ccc_id;
        return $clientChatChannel;
    }

    /**
     * @param ClientChat $clientChat
     */
    public function sendRequestToUsers(ClientChat $clientChat): void
    {
        $channel = $clientChat->cchChannel;

        $employeeSearch = new EmployeeSearch();
        $limit = $channel->getSystemUserLimit();
        $users = $employeeSearch->searchAvailableAgentsForChatRequests($clientChat, $limit, $channel->getSortParameters());

//        $key = self::getRedisDistributionLogicKey($clientChat->cch_id);
//        if ($users) {

        if (SettingHelper::isClientChatDebugEnable() && $clientChat->isTransfer()) {
            \Yii::info([
                'message' => 'Users will be assign to chat',
                'chatId' => $clientChat->cch_id,
                'chatStatus' => $clientChat->getStatusName(),
                'users' => ArrayHelper::getColumn($users, 'id'),
                'countUsers' => count($users),
                'microTime' => microtime(true),
                'date' => date('Y-m-d H:i:s'),
            ], 'info\ClientChatDebug');
        }

        foreach ($users as $user) {
            $this->sendRequestToUser($clientChat, $user->id);
        }

//            if ($limit) {
        $this->createUserAccessDistributionLogicJob($clientChat->cch_id, $channel->getSystemRepeatDelaySeconds());
//            }
//        } elseif (\Yii::$app->redis->exists($key)) {
//            \Yii::$app->redis->del($key);
//        }
    }

    public function createUserAccessDistributionLogicJob(int $chatId, int $delay = 0): void
    {
        $key = self::getRedisDistributionLogicKey($chatId);
        \Yii::$app->redis->set($key, true);
        \Yii::$app->redis->expire($key, self::REDIS_DISTRIBUTION_LOGIC_EXPIRE_S);

        $job = new ClientChatUserAccessJob();
        $job->chatId = $chatId;
        $job->delayJob = $delay;
        if (!$jobId = \Yii::$app->queue_client_chat_job->priority(10)->delay($delay)->push($job)) {
            throw new \RuntimeException('ClientChatUserAccessJob not added to queue. ChatId: ' .
                $chatId);
        }
    }

    public static function createJobAssigningUaToPendingChats(int $userId): void
    {
        $job = new ChatAssignUserAccessPendingChatsJob($userId);
        if (!$job->isRunning()) {
            \Yii::$app->queue_client_chat_job->priority(10)->push($job);
        }
        unset($job);
    }

    public function assignUserAccessToPendingChats(int $userId): void
    {
        $chats = ClientChatQuery::findAvailablePendingChatsByUser($userId)->all();
        if ($chats) {
            foreach ($chats as $chat) {
                if (!\Yii::$app->redis->exists(self::getRedisDistributionLogicKey($chat->cch_id))) {
                    $this->sendRequestToUser($chat, $userId);
                }
            }
        }
    }

    /**
     * @param ClientChat $clientChat
     * @param int $agentId
     */
    public function sendRequestToUser(ClientChat $clientChat, int $agentId): void
    {
        try {
            $clientChatUserAccess = ClientChatUserAccess::create($clientChat->cch_id, $agentId);
            $clientChatUserAccess->pending();
            $this->clientChatUserAccessRepository->save($clientChatUserAccess, $clientChat);
        } catch (\Throwable $e) {
            \Yii::error([
                'agentId' => $agentId,
                'chatId' => $clientChat->cch_id,
                'message' => $e->getMessage(),
            ], 'ClientChatService::sendRequestToUser');
        }
    }

    /**
     * @param string $rid
     * @param string $userId
     * @throws \yii\httpclient\Exception
     */
    public function assignAgentToRcChannel(string $rid, string $userId): void
    {
        $response = \Yii::$app->chatBot->assignAgent($rid, $userId);
        if ($response['error']) {
            if (mb_strpos($response['error']['message'] ?? '', 'error-selected-agent-room-agent-are-same')) {
                return;
            }

            throw new \RuntimeException('[Chat Bot Assign Agent] ' . $response['error']['message'] ?? 'Unknown error...', ClientChatCodeException::RC_ASSIGN_AGENT_FAILED);
        }
    }

    public function createByAgent(
        RealTimeStartChatForm $form,
        int $agentId,
        string $rcUserId,
        string $rcUserToken,
        ClientChatRequest $clientChatRequest,
        Client $client,
        ClientChatChannel $channel
    ): void {
        $_self = $this;
        try {
            $redis = \Yii::$app->redis;
            $key = $form->visitorId;

            if (!$redis->get($key)) {
                $redis->setnx($key, true);
            } else {
                throw new \RuntimeException('This action is currently being taken by another agent.', ClientChatCodeException::CC_REAL_TIME_ACTION_TAKEN);
            }

            $clientChat = $_self->clientChatRepository->getOrCreateByRequest($clientChatRequest, ClientChat::SOURCE_TYPE_AGENT);

            $dispatcher = \Yii::createObject(DeferredEventDispatcher::class);
            $dispatcher->defer();

            if (!$clientChat->cch_client_id) {
                $clientChat->cch_client_id = $client->id;
            }

            $clientChat->cch_channel_id = $channel->ccc_id;
            $clientChat->cch_project_id = $channel->ccc_project_id;
            $clientChat->cch_owner_user_id = $agentId;
            $clientChat->cch_client_online = 1;
            $clientChat->inProgress($agentId, ClientChatStatusLog::ACTION_OPEN_BY_AGENT);
            $this->clientChatRepository->save($clientChat);

            $this->transactionManager->wrap(static function () use ($form, $agentId, $_self, $rcUserId, $rcUserToken, $clientChatRequest, $clientChat) {
                $visitorRcId = $clientChatRequest->getClientRcId();
                try {
                    $visitorData = $_self->clientChatVisitorDataRepository->findByVisitorRcId($visitorRcId);
                    if (!$_self->clientChatVisitorRepository->exists($clientChat->cch_id, $visitorData->cvd_id)) {
                        $_self->clientChatVisitorRepository->create($clientChat->cch_id, $visitorData->cvd_id, $clientChat->cch_client_id);
                    }
                } catch (NotFoundException $e) {
                    $visitorData = $_self->clientChatVisitorDataRepository->createByVisitorId($visitorRcId);
                    $_self->clientChatVisitorRepository->create($clientChat->cch_id, $visitorData->cvd_id, $clientChat->cch_client_id);
                }

                $userChannel = $_self->clientChatUserChannelRepository->findByPrimaryKeys($agentId, $form->channelId);

                $clientChatUserAccess = ClientChatUserAccess::create($clientChat->cch_id, $userChannel->ccuc_user_id);
                $clientChatUserAccess->accept();
                $_self->clientChatUserAccessRepository->save($clientChatUserAccess, $clientChat);

                $rid = $_self->createRcRoom($form->visitorId, (string)$clientChat->cch_channel_id, $form->message, $rcUserId, $rcUserToken);

                $clientChat->cch_rid = $rid;
                $_self->clientChatRepository->save($clientChat);
            });

            $redis->del($key);
        } catch (\DomainException | \RuntimeException $e) {
            if (!ClientChatCodeException::isActionTaken($e)) {
                $redis->del($key);
            }

            if (isset($clientChat)) {
                $this->clientChatRepository->delete($clientChat);
            }
            throw $e;
        } catch (\Throwable $e) {
            $redis->del($key);
            throw $e;
        }
    }

    /**
     * @param ClientChatTransferForm $form
     * @param Employee $user
     * @return void
     * @throws \Throwable
     */
    public function transfer(ClientChatTransferForm $form, Employee $user): void
    {
        $this->transactionManager->wrap(function () use ($form, $user) {
            $clientChat = $this->clientChatRepository->findById($form->chatId);

            $transferRule = new ClientChatChannelTransferRule();
            if (!$transferRule->can($clientChat->cch_channel_id, $form->channelId)) {
                throw new \DomainException('Cant transfer to channel with other project');
            }

            if ($clientChat->isClosed()) {
                throw new \DomainException('It’s not possible to transfer the chat to another department because it is in the "Closed" status');
            }

            //          if ($clientChat->cch_dep_id === $form->depId && !$form->agentId) {
            //              throw new \DomainException('Chat already assigned to this department; Choose another;');
            //          }

            foreach ($form->agentId as $agentId) {
                if ($clientChat->cch_owner_user_id === $agentId) {
                    throw new \DomainException($clientChat->cchOwnerUser->nickname . ' is already the owner of this chat.');
                }
            }

            if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
                throw new \RuntimeException('Visitor RC id is not found');
            }

            $clientChatChannel = $this->clientChatChannelRepository->find($form->channelId);

            $activeChatExists = ClientChat::find()
                ->byChannel($clientChatChannel->ccc_id)
                ->byClientId($clientChat->cch_client_id)
                ->expectOwner((int)$clientChat->cch_owner_user_id)
                ->notInClosedGroup()
                ->exists();

            if ($activeChatExists && !$clientChatChannel->isAllowedTransferToChannel()) {
                throw new \DomainException('Client already has active chat in this department');
            }

            $clientChat->transfer($user->id, ClientChatStatusLog::ACTION_TRANSFER, $form->reasonId, $form->comment);
            $clientChat->cch_channel_id = $clientChatChannel->ccc_id;
            $this->clientChatRepository->save($clientChat);

            if ($form->isAgentTransfer()) {
                $agents = Employee::find()->joinChatUserChannel($clientChatChannel->ccc_id)->andWhere(['IN', 'id', $form->agentId])->all();
                if ($agents) {
                    /** @var Employee $agent */
                    foreach ($agents as $agent) {
                        try {
                            $this->sendRequestToUser($clientChat, $agent->id);
                        } catch (\RuntimeException $e) {
                            $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['agentId' => $agent->id]);
                            \Yii::warning($message, 'ClientChatService::transfer::RuntimeException');
                            throw $e;
                        }
                    }
                }
                $agentNames = Employee::find()->select(['nickname'])->where(['id' => $form->agentId])->asArray()->column();
                $transferTo = implode(', ', $agentNames) . ' agent';
            } else {
                $this->sendRequestToUsers($clientChat);
                $transferTo = $clientChatChannel->ccc_name . ' channel';
            }

            if ($clientChat->cch_owner_user_id !== $user->id) {
                $comment = $form->comment ? '; Comment: ' . $form->comment : '';
                $chatLink = Purifier::createChatShortLink($clientChat);
                if ($ntf = Notifications::create($clientChat->cch_owner_user_id, 'Your Chat was transferred', $user->nickname . ' starts chat transfer to ' . $transferTo . $comment . '; ' . $chatLink, Notifications::TYPE_INFO, true)) {
                    $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
                }
            }
        });
    }

    /**
     * @param ClientChat $clientChat
     * @param int $ownerId
     * @throws \yii\httpclient\Exception
     */
    public function acceptChat(ClientChat $clientChat, int $ownerId): void
    {
        $_self = $this;
        $this->transactionManager->wrap(static function () use ($_self, $clientChat, $ownerId) {
            $clientChat->assignOwner($ownerId)->inProgress($ownerId, ClientChatStatusLog::ACTION_CHAT_ACCEPT);
            $_self->clientChatRepository->save($clientChat);
            $_self->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
            $_self->assignAgentToRcChannel($clientChat->cch_rid, $clientChat->cchOwnerUser->userClientChatData->getRcUserId() ?? '');
        });
    }

    public function acceptFromMultipleUpdate(ClientChat $clientChat, int $ownerId): void
    {
        $_self = $this;
        $this->transactionManager->wrap(static function () use ($_self, $clientChat, $ownerId) {
            $clientChat->assignOwner($ownerId)->inProgress($ownerId, ClientChatStatusLog::ACTION_MULTIPLE_ACCEPT);
            $_self->clientChatRepository->save($clientChat);
            $_self->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
            $_self->assignAgentToRcChannel($clientChat->cch_rid, $clientChat->cchOwnerUser->userClientChatData->getRcUserId() ?? '');

            if ($access = ClientChatUserAccess::find()->byChatId($clientChat->cch_id)->byUserId($ownerId)->one()) {
                /** @var ClientChatUserAccess $access */
                $access->setStatus(ClientChatUserAccess::STATUS_ACCEPT);
                $_self->clientChatUserAccessRepository->save($access, $clientChat);
            }

            $clientChatUserAccessService = \Yii::$container->get(ClientChatUserAccessService::class);
            $clientChatUserAccessService->disableAccessForOtherUsersBatch($clientChat->cch_id, $ownerId);
        });
    }

    public function finishTransfer(ClientChat $clientChat, ClientChatUserAccess $chatUserAccess): ClientChat
    {
        return $this->transactionManager->wrap(function () use ($clientChat, $chatUserAccess) {
            $previous = $this->clientChatStatusLogRepository->getPrevious($clientChat->cch_id);
            $oldChannelId = $previous->csl_prev_channel_id ?? null;
            if (!$oldChannelId) {
                throw new \RuntimeException('Unable to determine the previous chat channel');
            }
            $lastMessage = $this->clientChatLastMessageRepository->getByChatId($clientChat->cch_id);

            $clientChat->archive(
                $chatUserAccess->ccua_user_id,
                ClientChatStatusLog::ACTION_ACCEPT_TRANSFER,
                null,
                null,
                true
            );
            $dto = ClientChatCloneDto::feelInOnTransfer($clientChat);
            $clientChat->changeChannel($oldChannelId);
            $this->clientChatRepository->save($clientChat);

            $newClientChat = ClientChat::clone($dto);
            $newClientChat->assignOwner($chatUserAccess->ccua_user_id);
            $newClientChat->cch_source_type_id = ClientChat::SOURCE_TYPE_TRANSFER;
            if (!$clientChat->cchChannel) {
                $channel = $this->clientChatChannelRepository->findDefaultByProject((int)$clientChat->cch_project_id);
                $newClientChat->cch_channel_id = $channel->ccc_id;
            }
            $this->clientChatRepository->save($newClientChat);
            $this->cloneLead($clientChat, $newClientChat)->cloneCase($clientChat, $newClientChat)->cloneNotes($clientChat, $newClientChat);
            $newClientChat->cch_parent_id = $clientChat->cch_id;
            $newClientChat->inProgress($chatUserAccess->ccua_user_id, ClientChatStatusLog::ACTION_ACCEPT_TRANSFER);
            $this->clientChatRepository->save($newClientChat);

            $prevCount = $clientChat->unreadMessage ? $clientChat->unreadMessage->ccu_count : 0;
            $unreadMessages = ClientChatUnread::create($newClientChat->cch_id, $prevCount, new \DateTimeImmutable());
            $this->clientChatUnreadRepository->save($unreadMessages);

            $userAccess = ClientChatUserAccess::create($newClientChat->cch_id, $newClientChat->cch_owner_user_id);
            $userAccess->accept();
            $this->clientChatUserAccessRepository->save($userAccess, $newClientChat);

            $oldVisitor = $clientChat->ccv->ccvCvd ?? null;

            if ($oldVisitor) {
                $this->clientChatVisitorRepository->create($newClientChat->cch_id, $oldVisitor->cvd_id, $newClientChat->cch_client_id);
            }
//            $chatUserAccess->transferAccept();

            if ((int)$oldChannelId !== (int)$newClientChat->cch_channel_id) {
                $botTransferChatResult = \Yii::$app->chatBot->transferDepartment($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id, (string)$oldChannelId, (string)$newClientChat->cch_channel_id);
                if ($botTransferChatResult['error']) {
                    throw new \RuntimeException('[Chat Bot Transfer] ' . $botTransferChatResult['error']['message'] ?? 'Cant read error message from Chat Bot response');
                }

                $success = $botTransferChatResult['data']['success'] ?? false;
                if (!$success) {
                    throw new \RuntimeException('[Chat Bot Transfer] ' . ($botTransferChatResult['data']['message'] ?? 'Cant read error message from Chat Bot response'));
                }
            }

            $this->assignAgentToRcChannel($newClientChat->cch_rid, $newClientChat->cchOwnerUser->userClientChatData->getRcUserId() ?? '');

            if ($lastMessage) {
                $lastMessageNew = $this->clientChatLastMessageRepository->cloneToNewChat($lastMessage, $newClientChat->cch_id);
                $this->clientChatLastMessageRepository->save($lastMessageNew);
            }

            $data = ClientChatAccessMessage::agentTransferAccepted($clientChat, $userAccess->ccuaUser);
            Notifications::publish('refreshChatPage', ['user_id' => $clientChat->cch_owner_user_id], ['data' => $data]);

            return $newClientChat;
        });
    }

    public function cancelTransfer(ClientChat $clientChat, ?Employee $user, int $action): void
    {
        $channel = $clientChat->cchChannel;
        if ($channel) {
            $previousLog = $this->clientChatStatusLogRepository->getPrevious($clientChat->cch_id);
            if (!$previousLog) {
                throw new \RuntimeException('Cannot find previous chat status log');
            }
            $clientChat->cch_channel_id = $previousLog->csl_prev_channel_id;
            $clientChat->inProgress($user->id ?? null, $action);
            $this->clientChatRepository->save($clientChat);

            Notifications::pub(
                [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                'refreshChatPage',
                ['data' => ClientChatAccessMessage::chatCanceledTransfer($clientChat, $user)]
            );
        }
    }

    public function closeConversation(ClientChatCloseForm $form, Employee $user): void
    {
        $clientChat = $this->clientChatRepository->findById($form->cchId);

        if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
            throw new \RuntimeException('Visitor RC id is not found');
        }

        if (SettingHelper::isClientChatSoftCloseEnabled()) {
            $clientChat->close($user->id, ClientChatStatusLog::ACTION_CLOSE, $form->reasonId, $form->comment);
        } else {
            $clientChat->archive($user->id, ClientChatStatusLog::ACTION_CLOSE, $form->reasonId, $form->comment);
        }

        $this->clientChatRepository->save($clientChat);

        if ($clientChat->cch_owner_user_id !== $user->id) {
            $comment = $form->comment ? '; Comment: ' . $form->comment : '';
            $chatLink = Purifier::createChatShortLink($clientChat);
            if ($ntf = Notifications::create($clientChat->cch_owner_user_id, 'Your Chat was closed', 'Your Chat was closed by ' . $user->nickname . $comment . '; ' . $chatLink, Notifications::TYPE_INFO, true)) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
            }
        }

        Notifications::pub(
            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
            'refreshChatPage',
            ['data' => ClientChatAccessMessage::chatClosed($clientChat, $user)]
        );
    }

    public function closeFromMultipleUpdate(int $cchId, Employee $user): void
    {
        $clientChat = $this->clientChatRepository->findById($cchId);

        if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
            throw new \RuntimeException('Visitor RC id is not found');
        }

        if (SettingHelper::isClientChatSoftCloseEnabled()) {
            $clientChat->close($user->id, ClientChatStatusLog::ACTION_MULTIPLE_UPDATE_CLOSE);
        } else {
            $clientChat->archive($user->id, ClientChatStatusLog::ACTION_MULTIPLE_UPDATE_CLOSE);
        }

        $this->clientChatRepository->save($clientChat);

        if ($clientChat->cch_owner_user_id && $clientChat->cch_owner_user_id !== $user->id) {
            $chatLink = Purifier::createChatShortLink($clientChat);
            Notifications::createAndPublish(
                $clientChat->cch_owner_user_id,
                'Your Chat was closed',
                'Your Chat was closed by ' . $user->nickname . '; ' . $chatLink,
                Notifications::TYPE_INFO,
                true
            );
            Notifications::pub(
                [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                'refreshChatPage',
                ['data' => ClientChatAccessMessage::chatClosed($clientChat, $user)]
            );
        }
    }

    /**
     * @param ClientChat $oldClientChat
     * @param ClientChat $newClientChat
     * @return ClientChatService
     */
    public function cloneLead(ClientChat $oldClientChat, ClientChat $newClientChat): self
    {
        $leads = $oldClientChat->leads;
        foreach ($leads as $lead) {
            $clientChatLead = ClientChatLead::create($newClientChat->cch_id, $lead->id, new \DateTimeImmutable('now'));
            $this->clientChatLeadRepository->save($clientChatLead);
        }
        return $this;
    }

    /**
     * @param ClientChat $oldClientChat
     * @param ClientChat $newClientChat
     * @return ClientChatService
     */
    public function cloneCase(ClientChat $oldClientChat, ClientChat $newClientChat): self
    {
        $cases = $oldClientChat->cases;
        foreach ($cases as $case) {
            $clientChatCase = ClientChatCase::create($newClientChat->cch_id, $case->cs_id, new \DateTimeImmutable('now'));
            $this->clientChatCaseRepository->save($clientChatCase);
        }
        return $this;
    }

    public function cloneNotes(ClientChat $oldClientChat, ClientChat $newClientChat): self
    {
        $notes = $oldClientChat->notes;
        foreach ($notes as $note) {
            $clientChatNote = ClientChatNote::create($newClientChat->cch_id, $note->ccn_user_id, $note->ccn_note);
            $clientChatNote->ccn_created_dt = $note->ccn_created_dt;
            $clientChatNote->ccn_updated_dt = $note->ccn_updated_dt;
            $clientChatNote->ccn_deleted = $note->ccn_deleted;
            $this->clientChatNoteRepository->save($clientChatNote);
        }
        return $this;
    }

    /**
     * @param string $visitorId
     * @param string $channelId
     * @param string|null $message
     * @param string $userRcId
     * @param string $userRcToken
     * @return string
     */
    public function createRcRoom(string $visitorId, string $channelId, ?string $message, string $userRcId, string $userRcToken): string
    {
        $result = \Yii::$app->chatBot->createRoom($visitorId, $channelId, $message, $userRcId, $userRcToken);
        if ($result['error']) {
            if (empty($result['error']['message'])) {
                $error = 'Unknown ChatBot error message';
                \Yii::error([
                    'message' => 'Unknown ChatBot error message',
                    'error' => VarDumper::dumpAsString($result['error'])
                ], 'ClientChatService');
            } else {
                $error = $result['error']['message'];
            }
            throw new \RuntimeException('[ChatBot Create Room] ' . $error);
        }

        if (!$rid = (string)($result['data']['rid'] ?? null)) {
            throw new \RuntimeException('[ChatBot Create Room] RoomId is not created');
        }
        return $rid;
    }

    public function takeClientChat(ClientChat $clientChat, Employee $owner, int $action = ClientChatStatusLog::ACTION_TAKE): ClientChat
    {
        if (empty($clientChat->cch_owner_user_id)) {
            return $this->transactionManager->wrap(function () use ($clientChat, $owner, $action) {
                $clientChat->assignOwner($owner->id);
                $clientChat->inProgress($owner->id, $action);
                $this->clientChatRepository->save($clientChat);
                $this->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
                $this->assignAgentToRcChannel($clientChat->cch_rid, $owner->userClientChatData->getRcUserId() ?? '');
                Notifications::publish('refreshChatPage', ['user_id' => $clientChat->cch_owner_user_id], ['data' => ClientChatAccessMessage::chatTaken($clientChat, $owner->username)]);
                return $clientChat;
            });
        }

        if ($clientChat->isOwner($owner->id)) {
            return $this->transactionManager->wrap(function () use ($clientChat, $owner, $action) {
                $clientChat->inProgress($owner->id, $action);
                $this->clientChatRepository->save($clientChat);
                $this->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
                Notifications::publish('refreshChatPage', ['user_id' => $clientChat->cch_owner_user_id], ['data' => ClientChatAccessMessage::chatTaken($clientChat, $owner->username)]);
                return $clientChat;
            });
        }
        $lastMessage = $this->clientChatLastMessageRepository->getByChatId($clientChat->cch_id);
        return $this->transactionManager->wrap(function () use ($clientChat, $owner, $lastMessage, $action) {
            $clientChat->archive($owner->id, $action, null, null, true);
            $this->clientChatRepository->save($clientChat);

            $dto = ClientChatCloneDto::feelInOnTake($clientChat, $owner->id);
            $newClientChat = ClientChat::clone($dto);
            $newClientChat->inProgress($owner->id, $action);
            $this->clientChatRepository->save($newClientChat);

            $this->cloneLead($clientChat, $newClientChat)
                ->cloneCase($clientChat, $newClientChat)
                ->cloneNotes($clientChat, $newClientChat);

            $userAccess = ClientChatUserAccess::create($newClientChat->cch_id, $newClientChat->cch_owner_user_id);
            $userAccess->accept();
            $this->clientChatUserAccessRepository->save($userAccess, $newClientChat);

            if ($oldVisitor = $clientChat->ccv->ccvCvd ?? null) {
                $this->clientChatVisitorRepository->create(
                    $newClientChat->cch_id,
                    $oldVisitor->cvd_id,
                    $newClientChat->cch_client_id
                );
            }

            if ($lastMessage) {
                $lastMessageNew = $this->clientChatLastMessageRepository->cloneToNewChat($lastMessage, $newClientChat->cch_id);
                $this->clientChatLastMessageRepository->save($lastMessageNew);
            }

            $this->assignAgentToRcChannel($newClientChat->cch_rid, $owner->userClientChatData->getRcUserId() ?? '');

            return $newClientChat;
        });
    }

    public function autoReturn(ClientChat $clientChat): void
    {
        $clientChat->inProgress(null, ClientChatStatusLog::ACTION_AUTO_RETURN);
        $this->clientChatRepository->save($clientChat);
        $clientChatUserAccessService = \Yii::$container->get(ClientChatUserAccessService::class);
        $clientChatUserAccessService->deleteAccessForOtherUsersBatch($clientChat->cch_id, $clientChat->cch_owner_user_id);

        Notifications::pub(
            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
            'refreshChatPage',
            ['data' => ClientChatAccessMessage::chatAutoReturn($clientChat->cch_id)]
        );
    }

    public function autoReopen(ClientChat $clientChat): void
    {
        $clientChat->inProgress(null, ClientChatStatusLog::ACTION_AUTO_REOPEN);
        $this->clientChatRepository->save($clientChat);

        Notifications::pub(
            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
            'refreshChatPage',
            ['data' => ClientChatAccessMessage::chatAutoReopen($clientChat->cch_id)]
        );
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
            'reloadClientChatList'
        );
        if ($clientChat->cch_owner_user_id) {
            Notifications::createAndPublish(
                $clientChat->cch_owner_user_id,
                'Chat has been reopened',
                'Chat (' . $clientChat->cch_id . ') from ' . $clientChat->cchClient->getShortName() . ' has been reopened by client',
                Notifications::TYPE_INFO,
                true
            );
        }
    }

    public function addActiveConnection(int $connectionId, int $chatId): bool
    {
        if ($activeConnection = UserConnectionActiveChat::find()->andWhere(['ucac_conn_id' => $connectionId])->one()) {
            if ($activeConnection->ucac_chat_id === $chatId) {
                return true;
            }
            $activeConnection->ucac_chat_id = $chatId;
        } else {
            $activeConnection = new UserConnectionActiveChat();
            $activeConnection->ucac_chat_id = $chatId;
            $activeConnection->ucac_conn_id = $connectionId;
        }

        if (!$activeConnection->save()) {
            \Yii::error([
                'message' => 'Add user connection active chat',
                'model' => $activeConnection->getAttributes(),
                'errors' => $activeConnection->getErrors(),
            ], 'ClientChatController:actionAddActiveConnection');
            return false;
        }
        return true;
    }

    public function createChatBasedOnOld(ClientChatCloneDto $dto, ClientChat $oldChat): ClientChat
    {
        $newClientChat = ClientChat::clone($dto);
        $newClientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
        $this->clientChatRepository->save($newClientChat);

        $this->cloneLead($oldChat, $newClientChat)
            ->cloneCase($oldChat, $newClientChat)
            ->cloneNotes($oldChat, $newClientChat);


        $oldVisitor = $oldChat->ccv->ccvCvd ?? null;
        if ($oldVisitor) {
            $this->clientChatVisitorRepository->create($newClientChat->cch_id, $oldVisitor->cvd_id, $newClientChat->cch_client_id);
        }

        $lastMessage = $this->clientChatLastMessageRepository->getByChatId($oldChat->cch_id);
        if ($lastMessage) {
            $lastMessageNew = $this->clientChatLastMessageRepository->cloneToNewChat($lastMessage, $newClientChat->cch_id);
            $this->clientChatLastMessageRepository->save($lastMessageNew);
        }

        $this->sendRequestToUsers($newClientChat);

        return $newClientChat;
    }

    public static function getRedisDistributionLogicKey(int $chatId): string
    {
        return $chatId . self::REDIS_DISTRIBUTION_LOGIC_KEY;
    }
}
