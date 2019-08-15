<?php

namespace sales\services\client;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;

/**
 * Class ClientManageService
 *
 * @property ClientRepository $clientRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property ClientEmailRepository $clientEmailRepository
 */
class ClientManageService
{

    private $clientRepository;
    private $clientPhoneRepository;
    private $clientEmailRepository;

    /**
     * ClientManageService constructor.
     * @param ClientRepository $clientRepository
     * @param ClientPhoneRepository $clientPhoneRepository
     * @param ClientEmailRepository $clientEmailRepository
     */
    public function __construct(ClientRepository $clientRepository, ClientPhoneRepository $clientPhoneRepository, ClientEmailRepository $clientEmailRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->clientEmailRepository = $clientEmailRepository;
    }

    /**
     * @param ClientCreateForm $clientForm
     * @return Client
     */
    public function create(ClientCreateForm $clientForm): Client
    {
        $client = Client::create(
            $clientForm->firstName,
            $clientForm->middleName,
            $clientForm->lastName
        );
        $this->clientRepository->save($client);
        return $client;
    }

    /**
     * @param Client $client
     * @param PhoneCreateForm[] $phones
     */
    public function addPhones(Client $client, array $phones): void
    {
        foreach ($phones as $phone) {
            $this->addPhone($client, $phone);
        }
    }

    /**
     * @param Client $client
     * @param PhoneCreateForm $phone
     */
    public function addPhone(Client $client, PhoneCreateForm $phone): void
    {
        if (!$this->clientPhoneRepository->exists($client->id, $phone->phone)) {
            $phone = ClientPhone::create(
                $phone->phone,
                $client->id
            );
            $this->clientPhoneRepository->save($phone);
        }
    }

    /**
     * @param Client $client
     * @param EmailCreateForm[] $emails
     */
    public function addEmails(Client $client, array $emails): void
    {
        foreach ($emails as $email) {
            $this->addEmail($client, $email);
        }
    }

    /**
     * @param Client $client
     * @param EmailCreateForm $email
     */
    private function addEmail(Client $client, EmailCreateForm $email): void
    {
        if (!$this->clientEmailRepository->exists($client->id, $email->email)) {
            $email = ClientEmail::create(
                $email->email,
                $client->id
            );
            $this->clientEmailRepository->save($email);
        }
    }

    /**
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phones
     * @param ClientCreateForm|null $clientForm
     * @return Client
     */
    public function getOrCreate(array $phones, ?ClientCreateForm $clientForm = null): Client
    {
        foreach ($phones as $phone) {
            if (($clientPhone = $this->clientPhoneRepository->getByPhone($phone->phone)) && ($client = $clientPhone->client)) {
                return $client;
            }
        }

        if (!$clientForm) {
            $clientForm = new ClientCreateForm(['firstName' => 'ClientName']);
        }
        $client = $this->create($clientForm);
        $this->addPhones($client, $phones);

        return $client;
    }

}