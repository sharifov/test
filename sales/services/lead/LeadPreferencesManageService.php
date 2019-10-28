<?php


namespace sales\services\lead;


use common\models\Lead;
use common\models\LeadPreferences;
use sales\forms\lead\LeadPreferencesForm;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;

/**
 * Class LeadPreferencesManageService
 * @package sales\services\lead
 *
 * @property ServiceFinder $finder
 * @property LeadRepository $leadRepository
 * @property TransactionManager $transactionManager
 * @property LeadPreferencesRepository $leadPreferencesRepository
 */
class LeadPreferencesManageService
{
	private $finder;
	private $leadRepository;
	private $transactionManager;
	private $leadPreferencesRepository;

	/**
	 * LeadPreferencesManageService constructor.
	 * @param ServiceFinder $finder
	 * @param TransactionManager $transactionManager
	 * @param LeadRepository $leadRepository
	 * @param LeadPreferencesRepository $leadPreferencesRepository
	 */
	public function __construct(
		ServiceFinder $finder,
		TransactionManager $transactionManager,
		LeadRepository $leadRepository,
		LeadPreferencesRepository $leadPreferencesRepository)
	{
		$this->finder = $finder;
		$this->leadRepository = $leadRepository;
		$this->transactionManager = $transactionManager;
		$this->leadPreferencesRepository = $leadPreferencesRepository;
	}

	/**
	 * @param LeadPreferencesForm $form
	 * @param int|Lead $lead
	 * @throws \Throwable
	 */
	public function edit(LeadPreferencesForm $form, Lead $lead): void
	{
		$lead = $this->finder->leadFind($lead);
		$leadPreferences = $lead->leadPreferences;

		$this->transactionManager->wrap( function () use($form, $lead, $leadPreferences) {
			$lead->editDelayedChargeAndNote($form->delayedCharge, $form->notesForExperts);
			$this->leadRepository->save($lead);

			if ($leadPreferences) {
				$leadPreferences->edit(
					$form->marketPrice,
					$form->clientsBudget,
					$form->numberStops
				);
			} else {
				$leadPreferences = LeadPreferences::create($lead->id, $form->marketPrice, $form->clientsBudget, $form->numberStops);
			}
			$this->leadPreferencesRepository->save($leadPreferences);
		});
	}
}