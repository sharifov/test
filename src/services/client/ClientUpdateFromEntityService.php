<?php

namespace src\services\client;

use common\models\ClientEmail;
use common\models\ClientPhone;
use src\entities\cases\Cases;
use src\forms\cases\CasesAddEmailForm;
use src\forms\cases\CasesAddPhoneForm;
use src\forms\cases\CasesClientUpdateForm;
use src\services\client\ClientCreateForm;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\repositories\client\ClientEmailRepository;
use src\repositories\client\ClientPhoneRepository;
use src\services\cases\CasesManageService;
use src\services\ServiceFinder;
use src\services\TransactionManager;

/**
 * Class ClientUpdateFromEntityService
 *
 * @property ServiceFinder $finder
 * @property ClientEmailRepository $clientEmailRepository
 * @property ClientManageService $clientManageService
 * @property CasesManageService $casesManageService
 * @property TransactionManager $transactionManager
 * @property ClientPhoneRepository $clientPhoneRepository
 */
class ClientUpdateFromEntityService
{
    private $finder;
    private $clientEmailRepository;
    private $clientManageService;
    private $casesManageService;
    private $transactionManager;
    private $clientPhoneRepository;

    public function __construct(
        ServiceFinder $finder,
        ClientEmailRepository $clientEmailRepository,
        ClientManageService $clientManageService,
        CasesManageService $casesManageService,
        TransactionManager $transactionManager,
        ClientPhoneRepository $clientPhoneRepository
    ) {
        $this->finder = $finder;
        $this->clientEmailRepository = $clientEmailRepository;
        $this->clientManageService = $clientManageService;
        $this->casesManageService = $casesManageService;
        $this->transactionManager = $transactionManager;
        $this->clientPhoneRepository = $clientPhoneRepository;
    }

    /**
     * @param int|Cases $case
     * @param CasesAddEmailForm $form
     * @throws \Throwable
     */
    public function addEmailFromCase($case, CasesAddEmailForm $form): void
    {
        $case = $this->finder->caseFind($case);

        if (!$client = $case->client) {
            throw new \DomainException('Client not found (Client Id: ' . $case->cs_client_id . ')');
        }

        if ($this->clientEmailRepository->exists($client->id, $form->email)) {
            throw new \DomainException('This email already exists ("' . $form->email . '"), Client Id: ' . $case->client->id);
        }

        $this->transactionManager->wrap(function () use ($client, $form, $case) {

            $this->clientManageService->addEmail($client, new EmailCreateForm(['email' => $form->email, 'type' => $form->type]));
            $case->updateLastAction();
        });
    }

    /**
     * @param int|Cases $case
     * @param CasesAddPhoneForm $form
     * @throws \Throwable
     */
    public function addPhoneFromCase($case, CasesAddPhoneForm $form): void
    {
        $case = $this->finder->caseFind($case);

        if (!$client = $case->client) {
            throw new \DomainException('Client not found (Client Id: ' . $case->cs_client_id . ')');
        }

        if ($this->clientPhoneRepository->exists($client->id, $form->phone)) {
            throw new \DomainException('This phone already exists ("' . $form->phone . '"), Client Id: ' . $case->client->id);
        }

        $this->transactionManager->wrap(function () use ($client, $form, $case) {

            $this->clientManageService->addPhone($client, new PhoneCreateForm(['phone' => $form->phone, 'type' => $form->type]));
            $case->updateLastAction();
        });
    }

    /**
     * @param int|Cases $case
     * @param CasesClientUpdateForm $form
     * @throws \Throwable
     */
    public function updateClientFromCase($case, CasesClientUpdateForm $form): void
    {
        $case = $this->finder->caseFind($case);

        if (!$client = $case->client) {
            throw new \DomainException('Client not found (Client Id: ' . $case->cs_client_id . ')');
        }

        $this->transactionManager->wrap(function () use ($client, $form, $case) {

            $this->clientManageService->updateClient($client, new ClientCreateForm([
                'firstName' => $form->first_name,
                'lastName' => $form->last_name,
                'middleName' => $form->middle_name,
                'locale' => $form->locale,
                'marketingCountry' => $form->marketingCountry,
            ]));
            $case->updateLastAction();
        });
    }
}
