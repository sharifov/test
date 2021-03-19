<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEvent;

use common\models\Notifications;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use sales\services\TransactionManager;
use yii\helpers\Html;

/**
 * Class GuestUtteredEvent
 * @package sales\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property ClientChatRepository $clientChatRepository
 * @property TransactionManager $transactionManager
 */
class GuestDisconnectedEvent implements ChatRequestEvent
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

    public function process(ClientChatRequest $request): void
    {
        $this->transactionManager->wrap(function () use ($request) {
            $visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($request->getClientRcId());

            if ($visitorData->clientChatVisitors) {
                foreach ($visitorData->clientChatVisitors as $chatVisitor) {
                    $clientChat = $this->clientChatRepository->findById($chatVisitor->ccv_cch_id);
                    if ($clientChat->cch_client_online) {
                        $clientChat->cch_client_online = 0;
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
