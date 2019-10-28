<?php

namespace sales\services;

use common\models\Employee;
use common\models\Lead;
use common\models\LeadPreferences;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;

/**
 * Class ServiceFinder
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property CasesRepository $casesRepository
 */
class ServiceFinder
{

    private $leadRepository;
    private $userRepository;
    private $casesRepository;
	/**
	 * @var LeadPreferencesRepository
	 */
	private $leadPreferencesRepository;

	/**
	 * @param LeadRepository $leadRepository
	 * @param UserRepository $userRepository
	 * @param CasesRepository $casesRepository
	 * @param LeadPreferencesRepository $leadPreferencesRepository
	 */
    public function __construct(
    	LeadRepository $leadRepository,
		UserRepository $userRepository,
		CasesRepository $casesRepository,
		LeadPreferencesRepository $leadPreferencesRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->casesRepository = $casesRepository;
		$this->leadPreferencesRepository = $leadPreferencesRepository;
	}

    /**
     * @param int|Lead $lead
     * @return Lead
     */
    public function leadFind($lead): Lead
    {
        if (is_int($lead)) {
            return $this->leadRepository->find($lead);
        }
        if ($lead instanceof Lead) {
            return $lead;
        }
        throw new \InvalidArgumentException('$lead must be integer or Lead');
    }

    /**
     * @param int|LeadPreferences $leadPreferences
     * @return LeadPreferences
     */
    public function leadPreferences($leadPreferences): LeadPreferences
    {
        if (is_int($leadPreferences)) {
            return $this->leadPreferencesRepository->find($leadPreferences);
        }
        if ($leadPreferences instanceof LeadPreferences) {
            return $leadPreferences;
        }
        throw new \InvalidArgumentException('$leadPreferences must be integer or LeadPreferences');
    }

    /**
     * @param int|Cases $case
     * @return Cases
     */
    public function caseFind($case): Cases
    {
        if (is_int($case)) {
            return $this->casesRepository->find($case);
        }
        if ($case instanceof Cases) {
            return $case;
        }
        throw new \InvalidArgumentException('$case must be integer or Cases');
    }

    /**
     * @param int|Employee $user
     * @return Employee
     */
    public function userFind($user): Employee
    {
        if (is_int($user)) {
            return $this->userRepository->find($user);
        }
        if ($user instanceof Employee) {
            return $user;
        }
        $tryUser = (int)$user;
        if ($tryUser > 0) {
            return $this->userRepository->find($tryUser);
        }
        throw new \InvalidArgumentException('$user must be integer or Employee');
    }

}
