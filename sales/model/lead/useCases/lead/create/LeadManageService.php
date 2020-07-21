<?php
namespace sales\model\lead\useCases\lead\create;

use common\models\Client;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use sales\forms\lead\PreferencesCreateForm;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;
use sales\model\clientChatRequest\ClientShortInfo;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientManageService;
use sales\services\lead\LeadHashGenerator;
use sales\services\TransactionManager;
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
	 * LeadManageService constructor.
	 * @param TransactionManager $transactionManager
	 * @param CasesManageService $casesManageService
	 * @param CasesRepository $casesRepository
	 * @param ClientManageService $clientManageService
	 * @param LeadHashGenerator $leadHashGenerator
	 * @param LeadRepository $leadRepository
	 * @param LeadPreferencesRepository $leadPreferencesRepository
	 * @param ClientChatLeadRepository $clientChatLeadRepository
	 */
	public function __construct(
		TransactionManager $transactionManager,
		CasesManageService $casesManageService,
		CasesRepository $casesRepository,
		ClientManageService $clientManageService,
		LeadHashGenerator $leadHashGenerator,
		LeadRepository $leadRepository,
		LeadPreferencesRepository $leadPreferencesRepository,
        ClientChatLeadRepository $clientChatLeadRepository
	)
	{
		$this->transactionManager = $transactionManager;
		$this->casesManageService = $casesManageService;
		$this->casesRepository = $casesRepository;
		$this->clientManageService = $clientManageService;
		$this->leadHashGenerator = $leadHashGenerator;
		$this->leadRepository = $leadRepository;
		$this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->clientChatLeadRepository = $clientChatLeadRepository;
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
	): Lead
	{
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

            $lead = Lead::createManually(
                $client->id,
                $client->first_name,
                $client->last_name,
                null,
                null,
                null,
                null,
                $chat->cch_ip,
                $form->source,
                $form->projectId,
                null,
                null,
                null,
                $chat->cch_dep_id,
                null
            );
//            $clientShortInfo = new ClientShortInfo($chat->cchCcr);
//            $lead->offset_gmt = $clientShortInfo->utc_offset;
//            $lead->l_client_ua = $clientShortInfo->userAgent;
//            $lead->request_ip_detail = Json::encode($clientShortInfo->geo);

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

            return $lead;
        });

        return $lead;
    }
}
