<?php

namespace sales\model\lead\useCases\lead\create;

use common\models\Call;
use common\models\Client;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\Sources;
use common\models\VisitorLog;
use sales\forms\lead\PreferencesCreateForm;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;
use sales\model\clientChatRequest\ClientShortInfo;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use sales\model\leadData\entity\LeadData;
use sales\model\leadData\repository\LeadDataRepository;
use sales\model\leadDataKey\entity\LeadDataKey;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\model\visitorLog\useCase\CreateVisitorLog;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientManageService;
use sales\services\lead\LeadHashGenerator;
use sales\services\TransactionManager;
use thamtech\uuid\helpers\UuidHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class LeadManageService
 * @package sales\model\lead\useCases\lead\create
 *
 * @property TransactionManager $transactionManager
 * @property CasesManageService $casesManageService
 * @property CasesRepository $casesRepository
 * @property ClientManageService $clientManageService
 * @property LeadHashGenerator $leadHashGenerator
 * @property LeadRepository $leadRepository
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property ClientChatLeadRepository $clientChatLeadRepository
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property VisitorLogRepository $visitorLogRepository
 * @property LeadDataRepository $leadDataRepository
 * @property LeadUserConversionRepository $leadUserConversionRepository
 */
class LeadManageService
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var CasesManageService
     */
    private $casesManageService;
    /**
     * @var CasesRepository
     */
    private $casesRepository;
    /**
     * @var ClientManageService
     */
    private $clientManageService;
    /**
     * @var LeadHashGenerator
     */
    private $leadHashGenerator;
    /**
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var LeadPreferencesRepository
     */
    private $leadPreferencesRepository;
    /**
     * @var ClientChatLeadRepository
     */
    private $clientChatLeadRepository;
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;
    /**
     * @var VisitorLogRepository
     */
    private VisitorLogRepository $visitorLogRepository;
    /**
     * @var LeadDataRepository
     */
    private LeadDataRepository $leadDataRepository;
    /**
     * @var LeadUserConversionRepository
     */
    private LeadUserConversionRepository $leadUserConversionRepository;

    /**
     * LeadManageService constructor.
     * @param TransactionManager $transactionManager
     * @param CasesManageService $casesManageService
     * @param CasesRepository $casesRepository
     * @param ClientManageService $clientManageService
     * @param LeadHashGenerator $leadHashGenerator
     * @param LeadRepository $leadRepository
     * @param LeadPreferencesRepository $leadPreferencesRepository
     * @param ClientChatLeadRepository $clientChatLeadRepository
     * @param ClientChatVisitorDataRepository $clientChatVisitorDataRepository
     * @param VisitorLogRepository $visitorLogRepository
     * @param LeadDataRepository $leadDataRepository
     */
    public function __construct(
        TransactionManager $transactionManager,
        CasesManageService $casesManageService,
        CasesRepository $casesRepository,
        ClientManageService $clientManageService,
        LeadHashGenerator $leadHashGenerator,
        LeadRepository $leadRepository,
        LeadPreferencesRepository $leadPreferencesRepository,
        ClientChatLeadRepository $clientChatLeadRepository,
        ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
        VisitorLogRepository $visitorLogRepository,
        LeadDataRepository $leadDataRepository,
        LeadUserConversionRepository $leadUserConversionRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->casesManageService = $casesManageService;
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->leadHashGenerator = $leadHashGenerator;
        $this->leadRepository = $leadRepository;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->clientChatLeadRepository = $clientChatLeadRepository;
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->leadDataRepository = $leadDataRepository;
        $this->leadUserConversionRepository = $leadUserConversionRepository;
    }

    /**
     * @param LeadManageForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Throwable
     */
    public function createManuallyByDefault(LeadManageForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {
        $lead = $this->transactionManager->wrap(function () use ($form, $employeeId, $creatorId, $reason) {
            return $this->createManually($form, $employeeId, $creatorId, $reason);
        });

        return $lead;
    }

    /**
     * @param LeadManageForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     */
    private function createManually(
        LeadManageForm $form,
        int $employeeId,
        ?int $creatorId,
        ?string $reason
    ): Lead {
        $client = $this->clientManageService->getOrCreate([$form->phone], [$form->email], $form->client);

        $lead = Lead::createManually(
            $client->id,
            $form->client->firstName,
            $form->client->lastName,
            null,
            null,
            null,
            null,
            null,
            $form->source,
            $form->projectId,
            null,
            $form->clientPhone,
            $form->clientEmail,
            $form->depId,
            null
        );

        $lead->processing($employeeId, $creatorId, $reason);

        $hash = $this->leadHashGenerator->generate(
            null,
            $form->projectId,
            null,
            null,
            null,
            null,
            [$form->phone->phone],
            null
        );

        $lead->setRequestHash($hash);

        $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$form->phone]);

        $leadId = $this->leadRepository->save($lead);

        $this->createLeadPreferences($leadId, $form->preferences);

        if ($logId = (new CreateVisitorLog())->create($client, $lead)) {
            $lead->setVisitorLog($logId);
            $this->leadRepository->save($lead);
        }

        return $lead;
    }

    /**
     * @param int $leadId
     * @param PreferencesCreateForm $preferencesForm
     */
    private function createLeadPreferences(int $leadId, PreferencesCreateForm $preferencesForm): void
    {
        $preferences = LeadPreferences::create(
            $leadId,
            null,
            null,
            null,
            null
        );
        $this->leadPreferencesRepository->save($preferences);
    }

    public function createByClientChat(LeadCreateByChatForm $form, ClientChat $chat, ?int $userId): Lead
    {
        $lead = $this->transactionManager->wrap(function () use ($form, $chat, $userId) {
            if (!$client = $chat->cchClient) {
                throw new \DomainException('Client Chat not assigned with Client');
            }

            $chatVisitorData = $this->clientChatVisitorDataRepository->getOneByChatId($chat->cch_id);

            if ($chatVisitorData->getSourceCid()) {
                $visitorLog = VisitorLog::createByClientChatRequest($chatVisitorData->cvd_id, $chatVisitorData->decodedData);
                $visitorLog->vl_ga_client_id = $visitorLog->vl_ga_client_id ?? UuidHelper::uuid();
                $visitorLog->vl_ga_user_id = $visitorLog->vl_ga_user_id ?? $client->uuid;
            } else {
                try {
                    $lastVisitorLog = $this->visitorLogRepository->findLastByClientAndProject($chat->cch_client_id, $chat->cch_project_id);
                    $visitorLog = new VisitorLog();
                    $visitorLog->fillInByChatOrLogData($chatVisitorData->decodedData, $lastVisitorLog);
                    $visitorLog->vl_ga_user_id = $client->uuid;
                } catch (NotFoundException $e) {
                    $visitorLog = VisitorLog::createByClientChatRequest($chatVisitorData->cvd_id, $chatVisitorData->decodedData);
                    $visitorLog->vl_ga_client_id = UuidHelper::uuid();
                    $visitorLog->vl_ga_user_id = $client->uuid;
                }
            }
            $visitorLog->vl_client_id = $client->id;
            if (!$visitorLog->vl_project_id) {
                $visitorLog->vl_project_id = $chat->cch_project_id;
            }
            $this->visitorLogRepository->save($visitorLog);

            $source = Sources::find()->select(['id'])->where(['cid' => $visitorLog->vl_source_cid])->one();
            $ip = null;
            $gmtOffset = null;
            if ($chatVisitorData) {
                $ip = $chatVisitorData->getRequestIp();
                $gmtOffset = ClientChatHelper::formatOffsetUtcToLeadOffsetGmt($chatVisitorData->getOffsetUtc());
            }

            $lead = Lead::createByClientChat(
                $client->id,
                $client->first_name,
                $client->last_name,
                $chat->cch_ip,
                $source['id'] ?? null,
                $form->projectId,
                $chat->cchChannel->ccc_dep_id,
                $userId,
                $visitorLog->vl_id,
                $ip,
                $gmtOffset
            );

            $lead->processing($userId, $userId, LeadFlow::DESCRIPTION_CLIENT_CHAT_CREATE);

            $clientPhones = ArrayHelper::getColumn($client->clientPhones, 'phone');

            $hash = $this->leadHashGenerator->generate(
                null,
                $form->projectId,
                null,
                null,
                null,
                null,
                $clientPhones,
                null
            );

            $lead->setRequestHash($hash);

            $leadId = $this->leadRepository->save($lead);

            $visitorLog->vl_lead_id = $leadId;
            $this->visitorLogRepository->save($visitorLog);

            $this->createLeadPreferences($leadId, new PreferencesCreateForm());

            $clientChatLead = ClientChatLead::create($chat->cch_id, $lead->id, new \DateTimeImmutable('now'));

            $this->clientChatLeadRepository->save($clientChatLead);

            if ($crossSystemXp = $chatVisitorData->getCrossSystemXp()) {
                $leadData = LeadData::create($lead->id, LeadDataKey::KEY_CROSS_SYSTEM_XP, $chatVisitorData->getCrossSystemXp());
                $this->leadDataRepository->save($leadData);
            }

            return $lead;
        });

        return $lead;
    }

    public function createFromPhoneWidget(Call $call, Employee $user): Lead
    {
        $internalPhoneNumber = $call->isIn() ? $call->c_to : $call->c_from;
        $clientPhoneNumber = $call->isIn() ? $call->c_from : $call->c_to;

        $sourceId = null;
        if ($departmentPhoneProject = DepartmentPhoneProject::findOne(['dpp_phone_number' => $internalPhoneNumber])) {
            $sourceId = $departmentPhoneProject->dpp_source_id;
        }

        if (!$sourceId && ($project = $call->cProject) && $sources = $project->sources) {
            $sourceId = $sources[0]->id;
        }

        $lead = Lead::createManually(
            $call->c_client_id,
            $call->cClient->first_name,
            $call->cClient->last_name,
            null,
            null,
            null,
            null,
            null,
            $sourceId,
            $call->c_project_id,
            null,
            $clientPhoneNumber,
            null,
            $call->c_dep_id,
            null
        );
        $lead->processing($user->id, $user->id, LeadFlow::DESCRIPTION_MANUAL_FROM_CALL);

        $hash = $this->leadHashGenerator->generate(
            null,
            $call->c_project_id,
            null,
            null,
            null,
            null,
            [$clientPhoneNumber],
            null
        );
        $lead->setRequestHash($hash);
        $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$clientPhoneNumber]);

        return $this->transactionManager->wrap(function () use ($lead, $call, $user) {
            $leadId = $this->leadRepository->save($lead);

            $this->createLeadPreferences($leadId, new PreferencesCreateForm());

            if ($logId = (new CreateVisitorLog())->create($call->cClient, $lead)) {
                $lead->setVisitorLog($logId);
                $this->leadRepository->save($lead);
            }

            $leadUserConversion = LeadUserConversion::create(
                $leadId,
                $user->id,
                LeadUserConversionDictionary::DESCRIPTION_MANUAL
            );
            $this->leadUserConversionRepository->save($leadUserConversion);

            return $lead;
        });
    }
}
