<?php

namespace sales\services\client;

use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\entities\cases\Cases;
use sales\forms\cases\CasesAddEmailForm;
use sales\forms\cases\CasesAddPhoneForm;
use sales\forms\cases\CasesClientUpdateForm;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\services\cases\CasesManageService;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;

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
    )
    {
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

        $this->transactionManager->wrap(function() use ($client, $form, $case) {

            $this->clientManageService->addEmail($client, new EmailCreateForm(['email' => $form->email, 'type' => ClientEmail::EMAIL_NOT_SET]));
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

        $this->transactionManager->wrap(function() use ($client, $form, $case) {

            $this->clientManageService->addPhone($client, new PhoneCreateForm(['phone' => $form->phone, 'type' => ClientPhone::PHONE_NOT_SET]));
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

        $this->transactionManager->wrap(function() use ($client, $form, $case) {

            $this->clientManageService->updateClient($client, new ClientCreateForm([
                'firstName' => $form->first_name,
                'lastName' => $form->last_name,
                'middleName' => $form->middle_name,
            ]));
            $case->updateLastAction();

        });
    }
}
