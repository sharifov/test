<?php

namespace sales\services\client;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\model\client\ClientCodeException;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\services\ServiceFinder;

/**
 * Class ClientManageService
 *
 * @property ClientRepository $clientRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property ClientEmailRepository $clientEmailRepository
 * @property InternalPhoneGuard $internalPhoneGuard
 * @property ServiceFinder $finder
 */
class ClientManageService
{
    private $clientRepository;
    private $clientPhoneRepository;
    private $clientEmailRepository;
    private $internalPhoneGuard;
    private $finder;

    /**
     * ClientManageService constructor.
     * @param ClientRepository $clientRepository
     * @param ClientPhoneRepository $clientPhoneRepository
     * @param ClientEmailRepository $clientEmailRepository
     * @param InternalPhoneGuard $internalPhoneGuard
     * @param ServiceFinder $finder
     */
    public function __construct(
        ClientRepository $clientRepository,
        ClientPhoneRepository $clientPhoneRepository,
        ClientEmailRepository $clientEmailRepository,
        InternalPhoneGuard $internalPhoneGuard,
        ServiceFinder $finder
    )
    {
        $this->clientRepository = $clientRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->clientEmailRepository = $clientEmailRepository;
        $this->internalPhoneGuard = $internalPhoneGuard;
        $this->finder = $finder;
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
     * @return ClientPhone|null
     */
    public function addPhone(Client $client, PhoneCreateForm $phoneForm): ?ClientPhone
    {
        if (!$phoneForm->phone) {
            return null;
        }

        if (!$this->clientPhoneRepository->exists($client->id, $phoneForm->phone)) {
            $phone = ClientPhone::create(
                $phoneForm->phone,
                $client->id,
				$phoneForm->type ?? null,
                $phoneForm->comments ?? null,
                $phoneForm->cp_title ?? null
            );
            $this->clientPhoneRepository->save($phone);
            return $phone;
        }
        return null;
    }

    /**
     * @param PhoneCreateForm $form
     * @return ClientPhone
     */
    public function updatePhone(PhoneCreateForm $form): ClientPhone
    {
		$phone = $this->clientPhoneRepository->find($form->id);

		if ($form->phone !== null) {
			$phone->phone = $form->phone;
		}
		if ($form->type !== null) {
			$phone->type = $form->type;
		}
		if ($form->cp_title !== null) {
			$phone->cp_title = $form->cp_title;
		}
		$this->clientPhoneRepository->save($phone);
		return $phone;
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
				$emailForm->type,
				$emailForm->ce_title
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
		// $email->editEmail($form->email), $form->type);
		if ($form->email !== null) {
			$email->email = $form->email;
		}
		if ($form->type !== null) {
			$email->type = $form->type;
		}
		if ($form->ce_title !== null) {
			$email->ce_title = $form->ce_title;
		}

		$this->clientEmailRepository->save($email);
	}

	/**
     * @param int|Client $client
	 * @param ClientCreateForm $form
	 */
	public function updateClient(Client $client, ClientCreateForm $form): void
	{
		$client = $this->finder->clientFind($client);
		$client->edit($form->firstName, $form->lastName, $form->middleName);
		$this->clientRepository->save($client);
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
            throw new \DomainException('Cannot create client. Not added phones.', ClientCodeException::CLIENT_CREATE_NOT_ADD_PHONES);
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
            throw new \DomainException('Cannot create client. Not added emails.', ClientCodeException::CLIENT_CREATE_NOT_ADD_EMAILS);
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
        } catch (InternalPhoneException $e) {
            throw $e;
        } catch (\DomainException $e) {
            $client = $this->getOrCreateByEmails($emails, $clientForm);
        }

        $this->addPhones($client, $phones);
        $this->addEmails($client, $emails);

        return $client;
    }

	/**
	 * @param array $phoneNumbers
	 * @return int
	 */
    public function checkIfPhoneIsTest(array $phoneNumbers): int
	{
		$testPhones = \Yii::$app->params['settings']['test_phone_list'] ?? \Yii::$app->params['test_phone_list'];

		foreach ($phoneNumbers as $phoneNumber) {
			if (in_array($phoneNumber, $testPhones ?? [], false)) {
				return 1;
			}
		}

		return 0;
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
            throw new \DomainException('Phones is empty', ClientCodeException::CLIENT_PHONES_EMPTY);
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
            throw new \DomainException('Emails is empty', ClientCodeException::CLIENT_EMAILS_EMPTY);
        }

        return $emails;
    }
}
