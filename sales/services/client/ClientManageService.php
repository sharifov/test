<?php

namespace sales\services\client;

use common\models\Client;
use common\models\ClientPhone;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;

/**
 * Class ClientManageService
 *
 * @property ClientRepository $clientRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 */
class ClientManageService
{

    private $clientRepository;
    private $clientPhoneRepository;

    public function __construct(ClientRepository $clientRepository, ClientPhoneRepository $clientPhoneRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
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
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phonesForm
     * @param ClientCreateForm|null $clientForm
     * @return Client
     */
    public function getOrCreateClient(array $phonesForm, ?ClientCreateForm $clientForm = null): Client
    {
        foreach ($phonesForm as $phoneForm) {
            if (($clientPhone = $this->clientPhoneRepository->getByPhone($phoneForm->phone)) && ($client = $clientPhone->client)) {
                return $client;
            }
        }

        if (!$clientForm) {
            $clientForm = new ClientCreateForm(['firstName' => 'ClientName']);
        }
        $client = $this->create($clientForm);
        $this->addPhones($client, $phonesForm);

        return $client;
    }

}