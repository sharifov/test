<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\components\purifier\Purifier;
use common\models\Notifications;
use Exception;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\notification\NotificationMessage;
use sales\helpers\ErrorsToStringHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
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
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;

    private ClientChatFeedbackRepository $clientChatFeedbackRepository;

    /**
     * ClientChatRequestService constructor.
     * @param ClientChatRepository $clientChatRepository
     * @param ClientChatFeedbackRepository $clientChatFeedbackRepository
     */
    public function __construct(
        ClientChatRepository $clientChatRepository,
        ClientChatFeedbackRepository $clientChatFeedbackRepository
    ) {
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatFeedbackRepository = $clientChatFeedbackRepository;
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

        if (!$clientChatFeedback->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($clientChatFeedback), -1);
        }

        if ($this->clientChatFeedbackRepository->save($clientChatFeedback)) {
            self::sendFeedbackNotifications($clientChat);
        }
        return $clientChatFeedback;
    }

    private static function sendFeedbackNotifications(ClientChat $clientChat): void
    {
        $clientChatLink = Purifier::createChatShortLink($clientChat);
        if (
            $notification = Notifications::create(
                $clientChat->cch_owner_user_id,
                'Feedback received',
                'Feedback received. ' . 'Client Chat; ' . $clientChatLink,
                Notifications::TYPE_INFO,
                true
            )
        ) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ?
                NotificationMessage::add($notification) : [];

            Notifications::publish(
                'getNewNotification',
                ['user_id' => $clientChat->cch_owner_user_id],
                $dataNotification
            );
        }
    }
}
