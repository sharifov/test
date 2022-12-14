<?php

namespace src\services;

use common\models\Call;
use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadPreferences;
use src\entities\cases\Cases;
use src\repositories\call\CallRepository;
use src\repositories\cases\CasesRepository;
use src\repositories\client\ClientRepository;
use src\repositories\lead\LeadPreferencesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\user\UserRepository;

/**
 * Class ServiceFinder
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property CasesRepository $casesRepository
 * @property ClientRepository $clientRepository
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property CallRepository $callRepository
 */
class ServiceFinder
{
    private $leadRepository;
    private $userRepository;
    private $casesRepository;
    private $clientRepository;
    private $leadPreferencesRepository;
    private $callRepository;

    /**
     * @param LeadRepository $leadRepository
     * @param UserRepository $userRepository
     * @param CasesRepository $casesRepository
     * @param ClientRepository $clientRepository
     * @param LeadPreferencesRepository $leadPreferencesRepository
     * @param CallRepository $callRepository
     */
    public function __construct(
        LeadRepository $leadRepository,
        UserRepository $userRepository,
        CasesRepository $casesRepository,
        ClientRepository $clientRepository,
        LeadPreferencesRepository $leadPreferencesRepository,
        CallRepository $callRepository
    ) {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->casesRepository = $casesRepository;
        $this->clientRepository = $clientRepository;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->callRepository = $callRepository;
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
     * @param int|Client $client
     * @return Client
     */
    public function clientFind($client): Client
    {
        if (is_int($client)) {
            return $this->clientRepository->find($client);
        }
        if ($client instanceof Client) {
            return $client;
        }
        throw new \InvalidArgumentException('$client must be integer or Client');
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

    /**
     * @param int|Call $call
     * @return Call
     */
    public function callFind($call): Call
    {
        if (is_int($call)) {
            return $this->callRepository->find($call);
        }
        if ($call instanceof Call) {
            return $call;
        }
        throw new \InvalidArgumentException('$call must be integer or Call');
    }
}
