<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\components\CentrifugoService;
use common\components\purifier\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatFeedback\ClientChatFeedbackRepository;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\client\ClientManageService;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;
use sales\model\clientChatRequest\repository\ClientChatRequestRepository;
use Yii;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatRequestService
 * @package sales\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatMessageRepository $clientChatMessageRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatService $clientChatService
 * @property VisitorLogRepository $visitorLogRepository
 * @property TransactionManager $transactionManager
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatFeedbackRepository $clientChatFeedbackRepository
 * @property CacheInterface $cache
 */
class ClientChatRequestService
{

    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $clientChatRequestRepository;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientManageService
     */
    private ClientManageService $clientManageService;
    /**
     * @var ClientChatMessageRepository
     */
    private ClientChatMessageRepository $clientChatMessageRepository;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var VisitorLogRepository
     */
    private VisitorLogRepository $visitorLogRepository;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var ClientChatVisitorRepository
     */
    private ClientChatVisitorRepository $clientChatVisitorRepository;
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;
    /**
     * @var ClientChatChannelRepository
     */
    private ClientChatChannelRepository $clientChatChannelRepository;

    private ClientChatFeedbackRepository $clientChatFeedbackRepository;

    private CacheInterface $cache;

    /**
     * ClientChatRequestService constructor.
     * @param ClientChatRequestRepository $clientChatRequestRepository
     * @param ClientChatRepository $clientChatRepository
     * @param ClientManageService $clientManageService
     * @param ClientChatMessageRepository $clientChatMessageRepository
     * @param ClientChatMessageService $clientChatMessageService
     * @param ClientChatService $clientChatService
     * @param VisitorLogRepository $visitorLogRepository
     * @param TransactionManager $transactionManager
     * @param ClientChatVisitorRepository $clientChatVisitorRepository
     * @param ClientChatVisitorDataRepository $clientChatVisitorDataRepository
     * @param ClientChatChannelRepository $clientChatChannelRepository
     * @param ClientChatFeedbackRepository $clientChatFeedbackRepository
     */
    public function __construct(
        ClientChatRequestRepository $clientChatRequestRepository,
        ClientChatRepository $clientChatRepository,
        ClientManageService $clientManageService,
        ClientChatMessageRepository $clientChatMessageRepository,
        ClientChatMessageService $clientChatMessageService,
        ClientChatService $clientChatService,
        VisitorLogRepository $visitorLogRepository,
        TransactionManager $transactionManager,
        ClientChatVisitorRepository $clientChatVisitorRepository,
        ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
        ClientChatChannelRepository $clientChatChannelRepository,
        ClientChatFeedbackRepository $clientChatFeedbackRepository
    ) {
        $this->clientChatRequestRepository = $clientChatRequestRepository;
        $this->clientChatRepository = $clientChatRepository;
        $this->clientManageService = $clientManageService;
        $this->clientChatMessageRepository = $clientChatMessageRepository;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatService = $clientChatService;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->transactionManager = $transactionManager;
        $this->clientChatVisitorRepository = $clientChatVisitorRepository;
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
        $this->clientChatChannelRepository = $clientChatChannelRepository;
        $this->clientChatFeedbackRepository = $clientChatFeedbackRepository;
        $this->cache = Yii::$app->cache;
    }

    /**
     * @param ClientChatRequestApiForm $form
     * @throws \JsonException
     * @throws \Throwable
     */
    public function create(ClientChatRequestApiForm $form): void
    {
        $clientChatRequest = $this->createRequest($form);
        $this->processRequest($clientChatRequest);
    }

    public function processRequest(ClientChatRequest $clientChatRequest): void
    {
        $this->transactionManager->wrap(function () use ($clientChatRequest) {
            if ($clientChatRequest->isRoomConnected()) {
                $this->roomConnected($clientChatRequest);
            } elseif ($clientChatRequest->isGuestDisconnected()) {
                $this->guestDisconnected($clientChatRequest);
            } elseif ($clientChatRequest->isTrackEvent()) {
                $this->createOrUpdateVisitorData($clientChatRequest);
            } else {
                throw new \RuntimeException('Unknown event provided');
            }
        });
    }

    /**
     * @param ClientChatRequestApiForm $form
     * @throws \JsonException
     */
    public function createMessage(ClientChatRequestApiForm $form): void
    {
        $clientChatRequest = ClientChatRequest::createByApi($form);

        if ($clientChatRequest->isGuestUttered() || $clientChatRequest->isAgentUttered()) {
            $message = ClientChatMessage::createByApi($form, $clientChatRequest->ccr_event);
            $this->clientChatMessageRepository->save($message, 0);

            $clientChat = $this->findClientChat($form->data['rid'] ?? '');
//            $clientChat = $this->findClientChatByCache($form->data['rid'] ?? '');
            if ($clientChat) {
                $this->assignMessageToChat($message, $clientChat);

                if ($clientChat->isIdle() && $clientChatRequest->isGuestUttered()) {
                    $clientChat->inProgress($clientChat->cch_owner_user_id, ClientChatStatusLog::ACTION_AUTO_REOPEN);
                    $this->clientChatRepository->save($clientChat);
                }
            }
        } else {
            $this->clientChatRequestRepository->save($clientChatRequest);
            throw new \RuntimeException('Unknown event provided');
        }
    }

    private function findClientChat(string $rid): ?ClientChat
    {
        return $this->clientChatRepository->getLastByRid($rid);
    }

    public function findClientChatByCache(string $rid): ?ClientChat
    {
        $tag = 'TAG_MUST_BE_SET';
        $duration = 10;
        $key = 'FIND_LAST_CLIENT_CHAT-' . $rid;
        return $this->cache->getOrSet($key, function () use ($rid) {
            return $this->findClientChat($rid);
        }, $duration, new TagDependency(['tags' => $tag]));
    }

    /**
     * @param ClientChatRequestApiForm $form
     * @return ClientChatRequest
     * @throws \JsonException
     */
    public function createRequest(ClientChatRequestApiForm $form): ClientChatRequest
    {
        $clientChatRequest = ClientChatRequest::createByApi($form);
        return $this->clientChatRequestRepository->save($clientChatRequest);
    }

    public function createOrUpdateFeedback(string $rid, ?string $comment, ?int $rating): ClientChatFeedback
    {
        $clientChat = $this->clientChatRepository->findLastByRid($rid ?? '');

        if ($clientChatFeedback = $clientChat->feedback) {
            $clientChatFeedback->ccf_user_id = $clientChat->cch_owner_user_id;
            $clientChatFeedback->ccf_message = $comment;
            $clientChatFeedback->ccf_rating = $rating;
        } else {
            $clientChatFeedback = ClientChatFeedback::create(
                $clientChat->cch_id,
                $clientChat->cch_owner_user_id,
                $clientChat->cch_client_id,
                $rating,
                $comment
            );
        }

        if ($this->clientChatFeedbackRepository->save($clientChatFeedback)) {
            self::sendFeedbackNotifications($clientChat);
        }
        return $clientChatFeedback;
    }

    private static function sendFeedbackNotifications(ClientChat $clientChat): void
    {
        $clientChatLink = Purifier::createChatShortLink($clientChat);
        if ($notification = Notifications::create(
            $clientChat->cch_owner_user_id,
            'Feedback received',
            'Feedback received. ' . 'Client Chat; ' . $clientChatLink,
            Notifications::TYPE_INFO,
            true
        )) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ?
                NotificationMessage::add($notification) : [];

            Notifications::publish(
                'getNewNotification',
                ['user_id' => $clientChat->cch_owner_user_id],
                $dataNotification
            );
        }
    }

    private function guestDisconnected(ClientChatRequest $clientChatRequest): void
    {
        $visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($clientChatRequest->getClientRcId());

        if ($visitorData->clientChatVisitors) {
            foreach ($visitorData->clientChatVisitors as $chatVisitor) {
                $clientChat = $this->clientChatRepository->findById($chatVisitor->ccv_cch_id);
                if ($clientChat->cch_client_online) {
                    $clientChat->cch_client_online = 0;
                    $this->clientChatRepository->save($clientChat);
                    if ($clientChat->cch_owner_user_id) {
                        Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
                            'cchId' => $clientChat->cch_id,
                            'isOnline' => (int)$clientChat->cch_client_online,
                            'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param ClientChatRequest $clientChatRequest
     */
    private function roomConnected(ClientChatRequest $clientChatRequest): void
    {
        $clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest, ClientChat::SOURCE_TYPE_CLIENT);

        $clientChatCreated = $clientChat->cch_id ? false : true;

        if (!$clientChat->cch_client_id) {
            $client = $this->clientManageService->getOrCreateByClientChatRequest($clientChatRequest, (int)$clientChat->cch_project_id);
            $clientChat->cch_client_id = $client->id;
        }
        $clientChat->cch_client_online = 1;

        if (!$clientChat->cch_channel_id) {
            $channel = $this->clientChatService->assignClientChatChannel($clientChat, $clientChatRequest->getChannelIdFromData());
            if ($channel->ccc_project_id !== $clientChat->cch_project_id) {
                throw new \DomainException('Channel project does not match project from api request');
            }

            if (!$clientChat->cch_id) {
                $clientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
            }

            $this->clientChatRepository->save($clientChat);
            $this->clientChatService->sendRequestToUsers($clientChat, $channel);
        } else {
            if (!$clientChat->cch_id) {
                $clientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
            }
            $this->clientChatRepository->save($clientChat);
        }

        $visitorRcId = $clientChatRequest->getClientRcId();
        $this->manageChatVisitorData($clientChat->cch_id, $clientChat->cch_client_id, $visitorRcId, $clientChatRequest->getDecodedData());

        if ($clientChat->cch_owner_user_id) {
            Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
                'cchId' => $clientChat->cch_id,
                'isOnline' => (int)$clientChat->cch_client_online,
                'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
            ]);
        }

        if ($clientChatCreated) {
            $messages = $this->getFreeMessages($clientChat->cch_rid);
            foreach ($messages as $message) {
                $this->assignMessageToChat($message, $clientChat);
            }
        }
    }

    /**
     * @param string $rid
     * @return ClientChatMessage[]
     */
    public function getFreeMessages(string $rid): array
    {
        return ClientChatMessage::find()->andWhere(['ccm_rid' => $rid])->andWhere(['is', 'ccm_cch_id', null])->all();
    }

    public function assignMessageToChat(ClientChatMessage $message, ClientChat $clientChat): void
    {
        $ownerUserId = null;
        if ($message->isAgentUttered()) {
            $ownerUserId = $clientChat->cch_owner_user_id;
        }
        $message->assignToChat($clientChat->cch_id, $clientChat->cch_client_id, $ownerUserId);
        $this->clientChatMessageRepository->save($message, 0);

        $this->sendLastChatMessageToMonitor($clientChat, $message);

        if ($message->isGuestUttered()) {
            if ($clientChat->hasOwner() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userProfile->isRegisteredInRc()) {
                if (!UserConnectionActiveChat::find()->andWhere(['ucac_chat_id' => $clientChat->cch_id])->exists()) {
                    $countUnreadByChatMessages = $this->clientChatMessageService->increaseUnreadMessages($clientChat->cch_id);
                    $this->updateMessageInfoNotification($countUnreadByChatMessages, $clientChat, $message);
                } else {
                    $this->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
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
                $this->clientChatMessageService->increaseUnreadMessages($clientChat->cch_id);
            }
            (Yii::createObject(ClientChatLastMessageRepository::class))->createOrUpdateByMessage($message);
        } elseif ($message->isAgentUttered()) {
            $this->clientChatMessageService->touchUnreadMessage($clientChat->cch_id);
            if ($clientChat->hasOwner() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userProfile->isRegisteredInRc()) {
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

    private function updateMessageInfoNotification($countUnreadByChatMessages, ClientChat $clientChat, ClientChatMessage $message): void
    {
        Notifications::publish('clientChatUnreadMessage', ['user_id' => $clientChat->cch_owner_user_id], [
            'data' => [
                'cchId' => $clientChat->cch_id,
                'totalUnreadMessages' => $this->clientChatMessageService->getCountOfTotalUnreadMessagesByUser($clientChat->cch_owner_user_id) ?: '',
                'cchUnreadMessages' => $countUnreadByChatMessages,
                'soundNotification' => $this->clientChatMessageService->soundNotification($clientChat->cch_owner_user_id),
                'shortMessage' => StringHelper::truncate($message->getMessage(), 40, '...'),
                'messageOwner' => $message->isMessageFromClient() ? 'client' : 'agent',
                'moment' =>  round((time() - strtotime($message->ccm_sent_dt))),
            ]
        ]);
    }

    public function createOrUpdateVisitorData(ClientChatRequest $request): void
    {
        $cchVisitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($request->getClientRcId());
        $this->clientChatVisitorDataRepository->updateByClientChatRequest($cchVisitorData, $request->getDecodedData());

        try {
            $visitorLog = $this->visitorLogRepository->findByVisitorDataId($cchVisitorData->cvd_id);
            $this->visitorLogRepository->updateByClientChatRequest($visitorLog, $request->getDecodedData());
        } catch (NotFoundException $e) {
            $this->visitorLogRepository->createByClientChatRequest($cchVisitorData->cvd_id, $request->getDecodedData());
        }
    }

    public function updateDateTimeLastMessageNotification(ClientChat $clientChat, ClientChatMessage $message): void
    {
        $user = $clientChat->cchOwnerUser;
        $dateTime = $message->ccm_sent_dt;
        $formatter = new \Yii::$app->formatter;
        if ($user->timezone) {
            $formatter->timeZone = $user->timezone;
        }
        Notifications::publish('clientChatUpdateTimeLastMessage', ['user_id' => $clientChat->cch_owner_user_id], [
            'data' => [
                'dateTime' =>  $formatter->asRelativeTime(strtotime($dateTime)),
                'moment' =>  round((time() - strtotime($dateTime))),
                'cchId' => $clientChat->cch_id,
            ]
        ]);
    }

    public function sendLastChatMessageToMonitor(ClientChat $clientChat, ClientChatMessage $message): void
    {
        $data = [];
        $data['chat_id'] = $message->ccm_cch_id;
        $data['client_id'] = $message->ccm_client_id;
        $data['user_id'] = $message->ccm_user_id;
        $data['sent_dt'] = \Yii::$app->formatter->asDatetime(strtotime($message->ccm_sent_dt), 'php: Y-m-d H:i:s');
        $data['period'] = \Yii::$app->formatter->asRelativeTime(strtotime($message->ccm_sent_dt));
        $data['msg'] = $message->message;

        try {
            CentrifugoService::sendMsg(json_encode([
                'chatMessageData' => $data,
            ]), 'realtimeClientChatChannel');
        } catch (\Throwable $throwable) {
            Yii::error(
                VarDumper::dumpAsString($throwable),
                'ClientChatRequestService:sendLastChatMessageToMonitor'
            );
        }
    }

    /**
     * @param int $chatId
     * @param int $clientId
     * @param string $visitorRcId
     * @param array $data
     */
    private function manageChatVisitorData(int $chatId, int $clientId, string $visitorRcId, array $data): void
    {
        try {
            $visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($visitorRcId);
            $visitorData->updateByClientChatRequest($data);
            $this->clientChatVisitorDataRepository->save($visitorData);
            if (!$this->clientChatVisitorRepository->exists($chatId, $visitorData->cvd_id)) {
                $this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
            }
        } catch (NotFoundException $e) {
            $visitorData = $this->clientChatVisitorDataRepository->createByClientChatRequest($visitorRcId, $data);
            $this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
        }

        try {
            $visitorLog = $this->visitorLogRepository->findByVisitorDataId($visitorData->cvd_id);
            $visitorLog->updateByClientChatRequest($data);
            $this->visitorLogRepository->save($visitorLog);
        } catch (NotFoundException $e) {
            $this->visitorLogRepository->createByClientChatRequest($visitorData->cvd_id, $data);
        }
    }
}
