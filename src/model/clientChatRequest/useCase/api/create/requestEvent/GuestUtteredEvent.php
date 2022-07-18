<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use modules\featureFlag\FFlag;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatMessage\ClientChatMessageRepository;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatService\ClientChatService;
use src\services\TransactionManager;

/**
 * Class GuestUtteredEvent
 * @package src\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatMessageRepository $clientChatMessageRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatRequestApiForm $form
 * @property TransactionManager $transactionManager
 */
class GuestUtteredEvent implements ChatRequestEvent
{
    /**
     * @var ClientChatMessageRepository
     */
    private ClientChatMessageRepository $clientChatMessageRepository;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;

    /**
     * @var ClientChatRequestApiForm $form
     */
    public ClientChatRequestApiForm $form;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    public function __construct(
        ClientChatMessageRepository $clientChatMessageRepository,
        ClientChatMessageService $clientChatMessageService,
        ClientChatRepository $clientChatRepository,
        ClientChatService $clientChatService,
        TransactionManager $transactionManager
    ) {
        $this->clientChatMessageRepository = $clientChatMessageRepository;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatService = $clientChatService;
        $this->transactionManager = $transactionManager;
    }

    public function process(ClientChatRequest $request): void
    {
        $message = ClientChatMessage::createByApi($this->form, $request->ccr_event, $request->getPlatformId());

        $clientChat = $this->clientChatRepository->getLastByRid($this->form->data['rid'] ?? '');

        $this->transactionManager->wrap(function () use ($message, $clientChat) {
            $this->clientChatMessageRepository->save($message, 0);

            if ($clientChat) {
                $this->clientChatMessageService->assignMessageToChat($message, $clientChat);
                /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
                if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE)) {
                    $leads = $clientChat->getLeads()->all();
                    foreach ($leads as $lead) {
                        if (isset($lead) && $lead->isBusinessType()) {
                            LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob(
                                $lead->id,
                                LeadBusinessExtraQueueLogStatus::REASON_RECEIVED_MESSAGE_FROM_CHAT
                            );
                        }
                    }
                }
                if ($clientChat->isClosed() && $owner = $clientChat->cchOwnerUser) {
                    if ($owner->isOnline()) {
                        $this->clientChatService->autoReopen($clientChat);
                    } else {
                        $clientChat->archive(null, ClientChatStatusLog::ACTION_AUTO_CLOSE, null, null);
                        $dto = ClientChatCloneDto::feelInOnClone($clientChat);
                        $this->clientChatRepository->save($clientChat);
                        $dto->sourceTypeId = ClientChat::SOURCE_TYPE_GUEST_UTTERED;
                        $this->clientChatService->createChatBasedOnOld($dto, $clientChat);

                        Notifications::pub(
                            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                            'refreshChatPage',
                            ['data' => ClientChatAccessMessage::chatArchive($clientChat->cch_id)]
                        );
                        Notifications::pub(
                            [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                            'reloadClientChatList'
                        );
                    }
                } elseif ($clientChat->isArchive()) {
                    $dto = ClientChatCloneDto::feelInOnClone($clientChat);
                    $dto->sourceTypeId = ClientChat::SOURCE_TYPE_GUEST_UTTERED;
                    $this->clientChatService->createChatBasedOnOld($dto, $clientChat);
                }
            }
        });
    }

    public function getClassName(): string
    {
        return self::class;
    }
}
