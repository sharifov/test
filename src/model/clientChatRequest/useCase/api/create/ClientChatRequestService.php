<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\components\purifier\Purifier;
use common\models\Notifications;
use Exception;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\notification\NotificationMessage;
use src\helpers\ErrorsToStringHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatFeedback\ClientChatFeedbackRepository;
use src\model\clientChatFeedback\entity\ClientChatFeedback;
use src\model\clientChatLastMessage\ClientChatLastMessageRepository;
use src\model\clientChatMessage\ClientChatMessageRepository;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use src\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use src\repositories\clientChatChannel\ClientChatChannelRepository;
use src\repositories\NotFoundException;
use src\repositories\visitorLog\VisitorLogRepository;
use src\services\client\ClientManageService;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatService\ClientChatService;
use src\services\TransactionManager;
use src\model\clientChatRequest\repository\ClientChatRequestRepository;
use Yii;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatRequestService
 * @package src\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatFeedbackRepository $clientChatFeedbackRepository
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
