<?php
namespace sales\model\lead\useCases\lead\create;

use common\models\Client;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\VisitorLog;
use sales\forms\lead\PreferencesCreateForm;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;
use sales\model\clientChatRequest\ClientShortInfo;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
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
        VisitorLogRepository $visitorLogRepository
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

    public function createByClientChat(LeadCreateByChatForm $form, ClientChat $chat, int $userId): Lead
    {
        $lead = $this->transactionManager->wrap(function () use ($form, $chat, $userId) {
            if (!$client = $chat->cchClient) {
                throw new \DomainException('Client Chat not assigned with Client');
            }

            $lead = Lead::createByClientChat(
                $client->id,
                $client->first_name,
                $client->last_name,
                $chat->cch_ip,
                $form->source,
                $form->projectId,
                $chat->cchChannel->ccc_dep_id,
                $userId
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

            $this->createLeadPreferences($leadId, new PreferencesCreateForm());

            $clientChatLead = ClientChatLead::create($chat->cch_id, $lead->id, new \DateTimeImmutable('now'));

            $this->clientChatLeadRepository->save($clientChatLead);


            if (($chatVisitorData = $this->clientChatVisitorDataRepository->getOneByChatId($chat->cch_id)) && $chatVisitorData->getSourceCid()) {
                $this->visitorLogRepository->createByClientChatRequest($chatVisitorData->cvd_id, $chatVisitorData->decodedData);
            } else {
                try {
                    $visitorLog = $this->visitorLogRepository->findLastByClientAndProject($chat->cch_client_id, $chat->cch_project_id);
                    $newVisitorLog = $this->visitorLogRepository->clone($visitorLog);
                    $newVisitorLog->vl_source_cid = null;
                    $newVisitorLog->vl_ga_user_id = $client->uuid;
                } catch (NotFoundException $e) {
                    $newVisitorLog = new VisitorLog();
                    $newVisitorLog->vl_ga_client_id = UuidHelper::uuid();
                    $newVisitorLog->vl_ga_user_id = $client->uuid;
                }
                $this->visitorLogRepository->save($newVisitorLog);
            }

            return $lead;
        });

        return $lead;
    }
}
