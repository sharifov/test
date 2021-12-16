<?php

namespace sales\model\lead\useCases\lead\create;

use common\models\Client;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\Sources;
use common\models\VisitorLog;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\lead\LeadHashGenerator;
use thamtech\uuid\helpers\UuidHelper;
use yii\helpers\ArrayHelper;

/**
 * Class CreateLeadByChatDTO
 * @package sales\model\lead\useCases\lead\create
 *
 * @property LeadCreateByChatForm $form
 * @property ClientChat $chat
 * @property int|null $userId
 * @property ClientChatVisitorDataRepository $this->clientChatVisitorDataRepository
 * @property VisitorLogRepository $visitorLogRepository
 * @property LeadHashGenerator $leadHashGenerator
 * @property ClientChatVisitorData $chatVisitorData
 * @property Client $client
 * @property VisitorLog $visitorLog
 * @property Lead $lead
 */
class CreateLeadByChatDTO
{
    public LeadCreateByChatForm $form;
    public ClientChat $chat;
    public ?int $userId;

    public ClientChatVisitorData $chatVisitorData;
    public Client $client;
    public ?VisitorLog $visitorLog = null;
    public ?Lead $lead = null;

    private VisitorLogRepository $visitorLogRepository;
    private LeadHashGenerator $leadHashGenerator;

    public function __construct(LeadCreateByChatForm $form, ClientChat $chat, ?int $userId)
    {
        $this->form = $form;
        $this->chat = $chat;
        $this->userId = $userId;

        $this->visitorLogRepository = \Yii::createObject(VisitorLogRepository::class);
        $this->leadHashGenerator = \Yii::createObject(LeadHashGenerator::class);

        $this->chatVisitorData = \Yii::createObject(ClientChatVisitorDataRepository::class)->getOneByChatId($this->chat->cch_id);

        if (!$this->client = $this->chat->cchClient) {
            throw new \DomainException('Client Chat not assigned with Client');
        }
    }

    public function leadInProgressDataPrepare(): self
    {
        $this->prepare();
        $this->lead->processing($this->userId, $this->userId, LeadFlow::DESCRIPTION_CLIENT_CHAT_CREATE);
        return $this;
    }

    public function leadNewDataPrepare(): self
    {
        $this->prepare();
        $this->lead->new($this->userId, $this->userId, LeadFlow::DESCRIPTION_CLIENT_CHAT_CREATE);
        return $this;
    }

    private function prepare(): void
    {
        if ($this->chatVisitorData->getSourceCid()) {
            $this->visitorLog = VisitorLog::createByClientChatRequest($this->chatVisitorData->cvd_id, $this->chatVisitorData->decodedData);
            $this->visitorLog->vl_ga_client_id = $this->visitorLog->vl_ga_client_id ?? UuidHelper::uuid();
            $this->visitorLog->vl_ga_user_id = $this->visitorLog->vl_ga_user_id ?? $this->client->uuid;
        } else {
            try {
                $lastVisitorLog = $this->visitorLogRepository->findLastByClientAndProject($this->chat->cch_client_id, $this->chat->cch_project_id);
                $this->visitorLog = new VisitorLog();
                $this->visitorLog->fillInByChatOrLogData($this->chatVisitorData->decodedData, $lastVisitorLog);
                $this->visitorLog->vl_ga_user_id = $this->client->uuid;
            } catch (NotFoundException $e) {
                $this->visitorLog = VisitorLog::createByClientChatRequest($this->chatVisitorData->cvd_id, $this->chatVisitorData->decodedData);
                $this->visitorLog->vl_ga_client_id = UuidHelper::uuid();
                $this->visitorLog->vl_ga_user_id = $this->client->uuid;
            }
        }
        $this->visitorLog->vl_client_id = $this->client->id;
        if (!$this->visitorLog->vl_project_id) {
            $this->visitorLog->vl_project_id = $this->chat->cch_project_id;
        }

        $sourceId = null;
        $source = Sources::find()->select(['id'])->where(['cid' => $this->visitorLog->vl_source_cid, 'project_id' => $this->form->projectId])->asArray()->one();
        if ($source) {
            $sourceId = (int)$source['id'];
        } else {
            $source = Sources::getByProjectId($this->form->projectId);
            if ($source) {
                $sourceId = $source->id;
            }
        }

        $ip = $this->chatVisitorData->getRequestIp();
        $gmtOffset = ClientChatHelper::formatOffsetUtcToLeadOffsetGmt($this->chatVisitorData->getOffsetUtc());

        $this->lead = Lead::createByClientChat(
            $this->client->id,
            $this->client->first_name,
            $this->client->last_name,
            $this->chat->cch_ip,
            $sourceId,
            $this->form->projectId,
            $this->chat->cchChannel->ccc_dep_id,
            $this->userId,
            $this->visitorLog->vl_id,
            $ip,
            $gmtOffset
        );
        $clientPhones = ArrayHelper::getColumn($this->client->clientPhones, 'phone');
        $hash = $this->leadHashGenerator->generate(
            null,
            $this->form->projectId,
            null,
            null,
            null,
            null,
            $clientPhones,
            null
        );
        $this->lead->setRequestHash($hash);
    }
}
