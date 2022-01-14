<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEventCreator;

use common\components\jobs\clientChat\ClientChatRequestCreateJob;
use src\helpers\setting\SettingHelper;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\repository\ClientChatRequestRepository;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;

/**
 * Class ChatRequestEventCreator
 * @package src\model\clientChatRequest\useCase\api\create\requestEventCreator
 *
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatRequest $clientChatRequest
 */
abstract class ChatRequestEventCreator
{
    protected ClientChatRequest $clientChatRequest;

    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $clientChatRequestRepository;

    public function __construct(ClientChatRequestRepository $clientChatRequestRepository)
    {
        $this->clientChatRequestRepository = $clientChatRequestRepository;
    }

    abstract public function getEvent(): ChatRequestEvent;

    public function handle(ClientChatRequestApiForm $form): void
    {
        $this->clientChatRequest = ClientChatRequest::createByApi($form);
        $this->clientChatRequestRepository->save($this->clientChatRequest);

        $this->init();
    }

    protected function init(): void
    {
        $chatRequestEvent = $this->getEvent();
        if (SettingHelper::isEnabledClientChatJob()) {
            $job = new ClientChatRequestCreateJob();
            $job->requestId = $this->clientChatRequest->ccr_id;
            $job->requestEventClass = $chatRequestEvent->getClassName();
            if ($jobId = \Yii::$app->queue_client_chat_job->priority(10)->push($job)) {
                $this->clientChatRequest->ccr_job_id = $jobId;
                $this->clientChatRequest->save();
            } else {
                throw new \RuntimeException('ClientChatRequest not added to queue. ClientChatRequest RID : ' .
                    $this->clientChatRequest->ccr_rid);
            }
        } else {
            $chatRequestEvent->process($this->clientChatRequest);
        }
    }
}
