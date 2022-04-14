<?php

namespace src\model\lead\useCases\lead\create;

use common\models\Call;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\query\SourcesQuery;
use common\models\Sources;
use common\models\VisitorLog;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\forms\lead\PreferencesCreateForm;
use src\helpers\clientChat\ClientChatHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\services\CallLogLeadCreateService;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\clientChatLead\entity\ClientChatLeadRepository;
use src\model\clientChatRequest\ClientShortInfo;
use src\model\clientChatVisitor\entity\ClientChatVisitor;
use src\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use src\model\phoneList\entity\PhoneList;
use src\model\visitorLog\useCase\CreateVisitorLog;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadPreferencesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use src\repositories\visitorLog\VisitorLogRepository;
use src\services\cases\CasesManageService;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\lead\LeadHashGenerator;
use src\services\TransactionManager;
use thamtech\uuid\helpers\UuidHelper;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class LeadManageService
 * @package src\model\lead\useCases\lead\create
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
 * @property LeadUserConversionService $leadUserConversionService
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
     * @var LeadUserConversionService
     */
    private LeadUserConversionService $leadUserConversionService;

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
        LeadUserConversionService $leadUserConversionService
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
        $this->leadUserConversionService = $leadUserConversionService;
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
            null,
            Lead::TYPE_CREATE_MANUALLY
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

        $this->createLeadPreferences($leadId, $form->preferences, false);

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
    private function createLeadPreferences(int $leadId, PreferencesCreateForm $preferencesForm, bool $defaultCurrencyByDb = true): void
    {
        $preferences = LeadPreferences::create(
            $leadId,
            null,
            null,
            null,
            $preferencesForm->currency
        );
        $preferences->setDefaultCurrencyCodeIfNotSet($defaultCurrencyByDb);
        $this->leadPreferencesRepository->save($preferences);
    }

    public function createByClientChat(CreateLeadByChatDTO $dto): Lead
    {
        return $this->transactionManager->wrap(function () use ($dto) {
            $this->visitorLogRepository->save($dto->visitorLog);
            $dto->lead->l_visitor_log_id = $dto->visitorLog->vl_id;

            $leadId = $this->leadRepository->save($dto->lead);

            $dto->visitorLog->vl_lead_id = $leadId;
            $this->visitorLogRepository->save($dto->visitorLog);

            $this->createLeadPreferences($leadId, new PreferencesCreateForm());

            $clientChatLead = ClientChatLead::create($dto->chat->cch_id, $leadId, new \DateTimeImmutable('now'));

            $this->clientChatLeadRepository->save($clientChatLead);
            if ($crossSystemXp = $dto->chatVisitorData->getCrossSystemXp()) {
                $leadData = LeadData::create($leadId, LeadDataKeyDictionary::KEY_CROSS_SYSTEM_XP, $dto->chatVisitorData->getCrossSystemXp());
                $this->leadDataRepository->save($leadData);
            }
            return $dto->lead;
        });
    }

    public function createFromPhoneWidget(Call $call, Employee $user): Lead
    {
        $internalPhoneNumber = $call->getInternalPhoneNumber();
        $clientPhoneNumber = $call->getClientPhoneNumber();

        $sourceId = null;

        if ($internalPhoneNumber) {
            if ($call->isDirect() || $call->isRedirectCall()) {
                $project = $call->cProject;
                $projectParams = $project ? $project->getParams() : null;
                if ($projectParams && $source = SourcesQuery::getByCidOrDefaultByProject($projectParams->object->lead->default_cid_on_direct_call, $call->c_project_id)) {
                    $sourceId = $source->id;
                } else if ($source = SourcesQuery::getFirstSourceByProjectId($call->c_project_id)) {
                    $sourceId = $source->id;
                    \Yii::warning([
                        'message' => 'Lead creation from phone widget: Not found source by CID and not found default by project for Direct Call',
                        'callId' => $call->c_id,
                        'sourceCidFromSettings' => $projectParams->object->lead->default_cid_on_direct_call,
                        'projectId' => $call->c_project_id,
                        'currentCid' => $source->cid
                    ], 'LeadManageService:createFromPhoneWidget:defaultSourceCidDetecting');
                }
            } else {
                $source = (new Query())
                    ->select(['dpp_source_id'])
                    ->from(PhoneList::tableName())
                    ->innerJoin(DepartmentPhoneProject::tableName(), 'dpp_phone_list_id = pl_id')
                    ->andWhere(['pl_phone_number' => $internalPhoneNumber, 'pl_enabled' => true])
                    ->andWhere(['dpp_project_id' => $call->c_project_id, 'dpp_enable' => true])
                    ->one();
                if ($source && $source['dpp_source_id']) {
                    $sourceId = (int)$source['dpp_source_id'];
                }
            }
        }

        if (!$sourceId && $call->c_project_id) {
            $source = Sources::getByProjectId($call->c_project_id);
            if ($source) {
                $sourceId = $source->id;
            }
        }

        $lead = Lead::createManually(
            $call->c_client_id,
            $call->cClient->first_name,
            $call->cClient->last_name,
            null,
            1,
            null,
            null,
            null,
            $sourceId,
            $call->c_project_id,
            null,
            $clientPhoneNumber,
            null,
            $call->c_dep_id,
            null,
            Lead::TYPE_CREATE_MANUALLY_FROM_CALL
        );
        $lead->setCabinClassEconomy();
        $lead->processing($user->id, $user->id, LeadFlow::DESCRIPTION_MANUAL_FROM_CALL);

        $hash = $this->leadHashGenerator->generate(
            null,
            $call->c_project_id,
            $lead->adults,
            null,
            null,
            $lead->cabin,
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

            $this->leadUserConversionService->addAutomate(
                $leadId,
                $user->id,
                LeadUserConversionDictionary::DESCRIPTION_MANUAL,
                $user->id
            );

            $this->createLeadData($lead->id, $call->c_lead_id, $call->c_id);

            $this->updateLeadOnRelationActiveCalls($lead, $call);

            return $lead;
        });
    }

    public function createFromPhoneWidgetWithInvalidClient(fromPhoneWidgetWithInvalidClient\Form $form, Call $call): Lead
    {
        return $this->transactionManager->wrap(function () use ($call, $form) {
            $phones = [];
            if ($form->phone) {
                $phones[] = new PhoneCreateForm([
                    'phone' => $form->phone,
                    'type' => ClientPhone::PHONE_NOT_SET,
                ]);
            }
            $emails = [];
            if ($form->email) {
                $emails[] = new EmailCreateForm([
                    'email' => $form->email,
                    'type' => ClientEmail::EMAIL_NOT_SET,
                ]);
            }
            $client = $this->clientManageService->getOrCreate(
                $phones,
                $emails,
                new ClientCreateForm([
                    'firstName' => $form->firstName,
                    'middleName' => $form->middleName,
                    'lastName' => $form->lastName,
                    'projectId' => $form->getProjectId(),
                    'typeCreate' => Client::TYPE_CREATE_LEAD,
                ])
            );

            $lead = Lead::createManually(
                $client->id,
                $client->first_name,
                $client->last_name,
                null,
                1,
                null,
                null,
                null,
                $form->getSourceId(),
                $form->getProjectId(),
                null,
                $form->phone,
                $form->email,
                $form->getDepartmentId(),
                null,
                Lead::TYPE_CREATE_MANUALLY_FROM_CALL
            );
            $lead->setCabinClassEconomy();
            $lead->processing($form->getUserId(), $form->getUserId(), LeadFlow::DESCRIPTION_MANUAL_FROM_CALL);

            $hash = $this->leadHashGenerator->generate(
                null,
                $form->getProjectId(),
                $lead->adults,
                null,
                null,
                $lead->cabin,
                [$form->phone],
                null
            );
            $lead->setRequestHash($hash);
            if ($form->phone) {
                $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$form->phone]);
            }

            $this->leadRepository->save($lead);

            $this->createLeadPreferences($lead->id, new PreferencesCreateForm());

            if ($logId = (new CreateVisitorLog())->create($client, $lead)) {
                $lead->setVisitorLog($logId);
                $this->leadRepository->save($lead);
            }

            $this->leadUserConversionService->addAutomate(
                $lead->id,
                $form->getUserId(),
                LeadUserConversionDictionary::DESCRIPTION_MANUAL,
                $form->getUserId()
            );

            $this->createLeadData($lead->id, $call->c_lead_id, $call->c_id);

            $this->updateLeadOnRelationActiveCalls($lead, $call);

            return $lead;
        });
    }

    private function createLeadData(int $newLeadId, ?int $oldLeadId, int $callId): void
    {
        LeadDataCreateService::createByCallId($newLeadId, $callId);
        if ($oldLeadId) {
            CallLogLeadCreateService::create($callId, $oldLeadId);
        }
        CallLogLeadCreateService::create($callId, $newLeadId);
    }

    private function updateLeadOnRelationActiveCalls(Lead $lead, Call $call): void
    {
        $call->c_lead_id = $lead->id;
        $call->c_client_id = $lead->client_id;
        if (!$call->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($call));
        }

        $parentId = null;

        if ($call->c_parent_id) {
            $parent = Call::find()->byId($call->c_parent_id)->active()->one();
            if ($parent) {
                $parentId = $parent->c_id;
                $parent->c_lead_id = $lead->id;
                $parent->c_client_id = $lead->client_id;
                if (!$parent->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($parent));
                }
            }
        } else {
            $parentId = $call->c_id;
        }

        if (!$parentId) {
            return;
        }

        $children = Call::find()->byParentId($parentId)->active()->all();
        foreach ($children as $child) {
            if ($child->c_id === $call->c_id) {
                continue;
            }
            $child->c_lead_id = $lead->id;
            $child->c_client_id = $lead->client_id;
            if (!$child->save(false)) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($child));
            }
        }
    }
}
