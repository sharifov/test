<?php

namespace sales\services\clientChatService;

use common\components\purifier\Purifier;
use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Notifications;
use common\models\UserProfile;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\notification\NotificationMessage;
use sales\dispatchers\DeferredEventDispatcher;
use sales\forms\clientChat\RealTimeStartChatForm;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\close\ClientChatCloseForm;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\ClientChatCaseRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannelTransfer\ClientChatChannelTransferRule;
use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;
use sales\model\clientChatNote\ClientChatNoteRepository;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\repository\ClientChatRequestRepository;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatUnread\entity\ClientChatUnread;
use sales\model\clientChatUnread\entity\ClientChatUnreadRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\client\ClientManageService;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
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
        $userChannel = ClientChatUserChannel::find()->byChannelId($clientChat->cch_channel_id)->onlineUsers()->all();

        if ($userChannel) {
            /** @var ClientChatUserChannel $item */
            foreach ($userChannel as $item) {
                $this->sendRequestToUser($clientChat, $item);
            }
        }
    }

    /**
     * @param ClientChat $clientChat
     * @param ClientChatUserChannel $clientChatUserChannel
     */
    public function sendRequestToUser(ClientChat $clientChat, ClientChatUserChannel $clientChatUserChannel): void
    {
        if ($clientChat->cch_owner_user_id !== $clientChatUserChannel->ccuc_user_id && $clientChatUserChannel->ccucUser->userProfile && $clientChatUserChannel->ccucUser->userProfile->isRegisteredInRc()) {
            $clientChatUserAccess = ClientChatUserAccess::create($clientChat->cch_id, $clientChatUserChannel->ccuc_user_id);
            $clientChatUserAccess->pending();
            $this->clientChatUserAccessRepository->save($clientChatUserAccess, $clientChat);
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
     * @return Department
     * @throws \Throwable
     */
    public function transfer(ClientChatTransferForm $form, Employee $user): ClientChatChannel
    {
        return $this->transactionManager->wrap(function () use ($form, $user) {
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
                foreach ($form->agentId as $agentId) {
                    $userChannel = ClientChatUserChannel::find()->byChannelId($clientChatChannel->ccc_id)->byUserId($agentId)->one();
                    if ($userChannel) {
                        try {
                            $this->sendRequestToUser($clientChat, $userChannel);
                        } catch (\RuntimeException $e) {
                            \Yii::error('Send request to user ' . $userChannel->ccuc_user_id . ' failed... ' . $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'ClientChatService::transfer::RuntimeException');
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

            return $clientChatChannel;
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
            $_self->assignAgentToRcChannel($clientChat->cch_rid, $clientChat->cchOwnerUser->userProfile->up_rc_user_id ?? '');
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

            $this->assignAgentToRcChannel($newClientChat->cch_rid, $newClientChat->cchOwnerUser->userProfile->up_rc_user_id ?? '');

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
                ['data' => ClientChatAccessMessage::chatCanceled($clientChat, $user)]
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
            throw new \RuntimeException('[ChatBot Create Room] ' . $result['error']['message'] ?? 'Unknown ChatBot error message');
        }

        if (!$rid = (string)($result['data']['rid'] ?? null)) {
            throw new \RuntimeException('[ChatBot Create Room] RoomId is not created');
        }
        return $rid;
    }

    public function takeClientChat(ClientChat $clientChat, Employee $owner): ClientChat
    {
        $lastMessage = $this->clientChatLastMessageRepository->getByChatId($clientChat->cch_id);

        return $this->transactionManager->wrap(function () use ($clientChat, $owner, $lastMessage) {

            $clientChat->archive($owner->id, ClientChatStatusLog::ACTION_TAKE, null, null, true);
            $this->clientChatRepository->save($clientChat);

            $dto = ClientChatCloneDto::feelInOnTake($clientChat, $owner->id);
            $newClientChat = ClientChat::clone($dto);
            $newClientChat->inProgress($owner->id, ClientChatStatusLog::ACTION_TAKE);
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

            $this->assignAgentToRcChannel($newClientChat->cch_rid, $owner->userProfile->up_rc_user_id ?? '');

            return $newClientChat;
        });
    }

    public function autoReturn(ClientChat $clientChat): void
    {
        $clientChat->inProgress(null, ClientChatStatusLog::ACTION_AUTO_RETURN);
        $this->clientChatRepository->save($clientChat);

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
    }
}
