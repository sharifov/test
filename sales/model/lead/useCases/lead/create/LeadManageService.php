<?php
namespace sales\model\lead\useCases\lead\create;

use common\models\Lead;
use common\models\LeadPreferences;
use sales\forms\lead\PreferencesCreateForm;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientManageService;
use sales\services\lead\LeadHashGenerator;
use sales\services\TransactionManager;

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
	 * LeadManageService constructor.
	 * @param TransactionManager $transactionManager
	 * @param CasesManageService $casesManageService
	 * @param CasesRepository $casesRepository
	 * @param ClientManageService $clientManageService
	 * @param LeadHashGenerator $leadHashGenerator
	 * @param LeadRepository $leadRepository
	 * @param LeadPreferencesRepository $leadPreferencesRepository
	 */
	public function __construct(
		TransactionManager $transactionManager,
		CasesManageService $casesManageService,
		CasesRepository $casesRepository,
		ClientManageService $clientManageService,
		LeadHashGenerator $leadHashGenerator,
		LeadRepository $leadRepository,
		LeadPreferencesRepository $leadPreferencesRepository
	)
	{
		$this->transactionManager = $transactionManager;
		$this->casesManageService = $casesManageService;
		$this->casesRepository = $casesRepository;
		$this->clientManageService = $clientManageService;
		$this->leadHashGenerator = $leadHashGenerator;
		$this->leadRepository = $leadRepository;
		$this->leadPreferencesRepository = $leadPreferencesRepository;
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
}