<?php

namespace sales\model\clientChatUserAccess\useCase\manageRequest;

use common\components\jobs\clientChat\ChatDataRequestSearchFlightQuotesJob;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\permissions\ClientChatActionPermission;
use sales\model\clientChat\useCase\leadAutoTake\ClientChatLeadAutoTakeService;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequest;
use sales\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;

/**
 * Class UserAccessAcceptPending
 * @package sales\model\clientChatUserAccess\useCase\manageRequest
 *
 * @property ClientChat $chat
 * @property ClientChatUserAccess $access
 * @property int $accessStatusId
 * @property ClientChatUserAccessService $accessService
 * @property ClientChatActionPermission $actionPermission
 * @property ClientChatLeadAutoTakeService $leadAutoTakeService
 */
class UserAccessAcceptPending implements UserAccessManageRequestInterface
{
    private ClientChat $chat;
    private ClientChatUserAccess $access;
    private int $accessStatusId;
    private ClientChatUserAccessService $accessService;
    private ClientChatActionPermission $actionPermission;
    private ClientChatLeadAutoTakeService $leadAutoTakeService;

    public function __construct(ClientChat $chat, ClientChatUserAccess $access, int $accessStatusId)
    {
        $this->chat = $chat;
        $this->access = $access;
        $this->accessStatusId = $accessStatusId;
        $this->accessService = \Yii::createObject(ClientChatUserAccessService::class);
        $this->actionPermission = \Yii::createObject(ClientChatActionPermission::class);
        $this->leadAutoTakeService = \Yii::createObject(ClientChatLeadAutoTakeService::class);
    }

    public function handle(): void
    {
        if (!$this->actionPermission->canAcceptPending($this->chat)) {
            throw new \DomainException('Action is not allowed');
        }
        $this->accessService->acceptPending($this->chat, $this->access, $this->accessStatusId);

        $this->leadAutoTakeService->byAcceptFromWidget($this->chat->cch_id, $this->access->ccua_user_id);

        $chatDataRequest = ClientChatDataRequest::find()->byChatId($this->chat->cch_id)->one();

        $enabled = $this->chat->cchChannel->isSearchAndCacheFlightQuotesEnabled() ?? false;

        if ($enabled && $chatDataRequest && $chatDataRequest->ccdr_data_json && is_array($chatDataRequest->ccdr_data_json)) {
            $form = new FlightSearchDataRequestForm($chatDataRequest->ccdr_data_json);
            if ($form->validate()) {
                $job = new ChatDataRequestSearchFlightQuotesJob($form, $this->chat->cch_id, $this->chat->cch_project_id);
                \Yii::$app->queue_client_chat_job->priority(10)->push($job);
            }
        }
    }
}
