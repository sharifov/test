<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use common\models\Notifications;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use src\services\TransactionManager;
use yii\helpers\Html;

/**
 * Class GuestConnectedEvent
 * @package src\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property-read ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property-read ClientChatRepository $clientChatRepository
 * @property-read TransactionManager $transactionManager
 */
class GuestConnectedEvent implements ChatRequestEvent
{
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    public function __construct(
        ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
        ClientChatRepository $clientChatRepository,
        TransactionManager $transactionManager
    ) {
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
        $this->clientChatRepository = $clientChatRepository;
        $this->transactionManager = $transactionManager;
    }

    public function process(ClientChatRequest $entity): void
    {
        $this->transactionManager->wrap(function () use ($entity) {
            $visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($entity->getClientRcId());

            if ($visitorData->clientChatVisitors) {
                foreach ($visitorData->clientChatVisitors as $chatVisitor) {
                    $clientChat = $this->clientChatRepository->findById($chatVisitor->ccv_cch_id);
                    if (!$clientChat->cch_client_online) {
                        $clientChat->cch_client_online = 1;
                        $this->clientChatRepository->save($clientChat);
                        Notifications::pub(
                            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                            'clientChatUpdateClientStatus',
                            [
                                'cchId' => $clientChat->cch_id,
                                'isOnline' => (int)$clientChat->cch_client_online,
                                'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
                            ]
                        );
                    }
                }
            }
        });
    }

    public function getClassName(): string
    {
        return self::class;
    }
}
