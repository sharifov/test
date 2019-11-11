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
     * @param PhoneCreateForm $phoneForm
     */
    public function addPhone(Client $client, PhoneCreateForm $phoneForm): void
    {
        if (!$phoneForm->phone) {
            return;
        }
        if (!$this->clientPhoneRepository->exists($client->id, $phoneForm->phone)) {
            $phone = ClientPhone::create(
                $phoneForm->phone,
                $client->id,
				$phoneForm->type ?? null,
                $phoneForm->comments ?? null
            );
            $this->clientPhoneRepository->save($phone);
        }
    }

	/**
	 * @param PhoneCreateForm $form
	 */
    public function updatePhone(PhoneCreateForm $form): void
	{
		$phone = $this->clientPhoneRepository->find($form->id);
		$phone->edit($form->phone, $form->type);
		$this->clientPhoneRepository->save($phone);
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
     * @param EmailCreateForm $emailForm
     */
    public function addEmail(Client $client, EmailCreateForm $emailForm): void
    {
        if (!$emailForm->email) {
            return;
        }
        if (!$this->clientEmailRepository->exists($client->id, $emailForm->email)) {
            $email = ClientEmail::create(
                $emailForm->email,
                $client->id,
				$emailForm->type
            );
            $this->clientEmailRepository->save($email);
        }
    }

	/**
	 * @param EmailCreateForm $form
	 */
	public function updateEmail(EmailCreateForm $form): void
	{
		$email = $this->clientEmailRepository->find($form->id);
		$email->edit($form->email, $form->type);
		$this->clientEmailRepository->save($email);
	}

	/**
	 * @param ClientCreateForm $form
	 * @return Client
	 */
	public function updateClient(ClientCreateForm $form): Client
	{
		$client = $this->clientRepository->find($form->id);
		$client->edit($form->firstName, $form->lastName, $form->middleName);
		$this->clientRepository->save($client);
		return $client;
	}

    /**
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phones
     * @param ClientCreateForm|null $clientForm
     * @return Client
     */
    public function getOrCreateByPhones(array $phones, ?ClientCreateForm $clientForm = null): Client
    {
        $phones = $this->guardNotEmptyPhones($phones);

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

        if (!$client->clientPhones) {
            throw new \DomainException('Cannot create client. Not added phones.');
        }

        return $client;
    }

    /**
     * Find or create Client
     *
     * @param EmailCreateForm[] $emails
     * @param ClientCreateForm|null $clientForm
     * @return Client
     */
    public function getOrCreateByEmails(array $emails, ?ClientCreateForm $clientForm = null): Client
    {
        $emails = $this->guardNotEmptyEmails($emails);

        foreach ($emails as $email) {
            if (($clientEmail = $this->clientEmailRepository->getByEmail($email->email)) && ($client = $clientEmail->client)) {
                return $client;
            }
        }

        if (!$clientForm) {
            $clientForm = new ClientCreateForm(['firstName' => 'ClientName']);
        }

        $client = $this->create($clientForm);
        $this->addEmails($client, $emails);

        if (!$client->clientEmails) {
            throw new \DomainException('Cannot create client. Not added emails.');
        }

        return $client;
    }

    /**
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phones
     * @param EmailCreateForm[] $emails
     * @param ClientCreateForm|null $clientForm
     * @return Client
     */
    public function getOrCreate(array $phones, array $emails, ?ClientCreateForm $clientForm = null): Client
    {
        try {
            $client = $this->getOrCreateByPhones($phones, $clientForm);
        } catch (\DomainException $e) {
            $client = $this->getOrCreateByEmails($emails, $clientForm);
        }
        return $client;
    }

	/**
	 * @param string $phoneNumber
	 * @return bool
	 */
    public function checkIfPhoneIsTest(string $phoneNumber): bool
	{
		$testPhones = \Yii::$app->params['settings']['test_phone_list'] ?? \Yii::$app->params['test_phone_list'];

		return in_array($phoneNumber, $testPhones ?? [], false);
	}

    /**
     * @param PhoneCreateForm[] $clientPhones
     * @return array
     */
    private function guardNotEmptyPhones(array $clientPhones): array
    {
        $phones = array_filter($clientPhones, function (PhoneCreateForm $element) {
            return $element->phone;
        });

        if (!$phones) {
            throw new \DomainException('Phones is empty');
        }

        return $phones;
    }

    /**
     * @param EmailCreateForm[] $clientEmails
     * @return array
     */
    private function guardNotEmptyEmails(array $clientEmails): array
    {
        $emails = array_filter($clientEmails, function (EmailCreateForm $element) {
            return $element->email;
        });

        if (!$emails) {
            throw new \DomainException('Emails is empty');
        }

        return $emails;
    }

}
