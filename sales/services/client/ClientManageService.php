<?php

namespace sales\services\client;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\UserContactList;
use http\Exception\RuntimeException;
use sales\model\clientAccount\entity\ClientAccount;
use sales\model\clientAccount\form\ClientAccountCreateApiForm;
use sales\repositories\client\ClientsCollection;
use sales\repositories\client\ClientsQuery;
use sales\services\client\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\model\client\ClientCodeException;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\repositories\NotFoundException;
use sales\services\ServiceFinder;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class ClientManageService
 *
 * @property ClientRepository $clientRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property ClientEmailRepository $clientEmailRepository
 * @property InternalPhoneGuard $internalPhoneGuard
 * @property ServiceFinder $finder
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 */
class ClientManageService
{
    private $clientRepository;
    private $clientPhoneRepository;
    private $clientEmailRepository;
    private $internalPhoneGuard;
    private $finder;
    /**
     * @var ClientChatVisitorRepository
     */
    private ClientChatVisitorRepository $clientChatVisitorRepository;

    /**
     * ClientManageService constructor.
     * @param ClientRepository $clientRepository
     * @param ClientPhoneRepository $clientPhoneRepository
     * @param ClientEmailRepository $clientEmailRepository
     * @param InternalPhoneGuard $internalPhoneGuard
     * @param ServiceFinder $finder
     * @param ClientChatVisitorRepository $clientChatVisitorRepository
     */
    public function __construct(
        ClientRepository $clientRepository,
        ClientPhoneRepository $clientPhoneRepository,
        ClientEmailRepository $clientEmailRepository,
        InternalPhoneGuard $internalPhoneGuard,
        ServiceFinder $finder,
        ClientChatVisitorRepository $clientChatVisitorRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->clientEmailRepository = $clientEmailRepository;
        $this->internalPhoneGuard = $internalPhoneGuard;
        $this->finder = $finder;
        $this->clientChatVisitorRepository = $clientChatVisitorRepository;
    }

    /**
     * @param ClientCreateForm $clientForm
     * @param int|null $parentId
     * @return Client
     */
    public function create(ClientCreateForm $clientForm, ?int $parentId): Client
    {
        $client = Client::create(
            $clientForm->firstName,
            $clientForm->middleName,
            $clientForm->lastName,
            $clientForm->projectId,
            $clientForm->typeCreate,
            $parentId,
            $clientForm->ip
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
        $client->edit(
            (string) $form->firstName,
            (string) $form->lastName,
            (string) $form->middleName,
            (string) $form->locale,
            (string) $form->marketingCountry
        );
        $this->clientRepository->save($client);
    }

    /**
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phones
     * @param ClientCreateForm $clientForm
     * @return Client
     */
    public function getOrCreateByPhones(array $phones, ClientCreateForm $clientForm): Client
    {
        $phones = $this->guardNotEmptyPhones($phones);

        $parentId = null;
        $projectId = (int)$clientForm->projectId;

        foreach ($phones as $phone) {
            $collections = new ClientsCollection(ClientsQuery::allByPhone($phone->phone));
            if ($collections->isEmpty()) {
                continue;
            }
            if ($projectId) {
                if ($client = $collections->getWithProject($projectId)) {
                    return $client;
                }
            } else {
                if ($client = $collections->getWithoutProject()) {
                    return $client;
                }
            }
            $parentId = $collections->getFirstId();
            break;
        }

        $client = $this->create($clientForm, $parentId);
        $this->addPhones($client, $phones);

        if (!$client->clientPhones) {
            throw new \DomainException('Cannot create client. Not added phones.', ClientCodeException::CLIENT_CREATE_NOT_ADD_PHONES);
        }

        return $client;
    }

    /**
     * @param PhoneCreateForm[] $phones
     * @param ClientCreateForm $form
     * @return Client
     */
    public function getExistingOrCreateEmptyObj(array $phones, ClientCreateForm $form): Client
    {
        $parentId = null;
        $projectId = (int)$form->projectId;

        foreach ($phones as $phone) {
            $collections = new ClientsCollection(ClientsQuery::allByPhone($phone->phone));
            if ($collections->isEmpty()) {
                continue;
            }
            if ($projectId) {
                if ($client = $collections->getWithProject($projectId)) {
                    return $client;
                }
            } else {
                if ($client = $collections->getWithoutProject()) {
                    return $client;
                }
            }
            $parentId = $collections->getFirstId();
            break;
        }

        $client = Client::create(
            '',
            '',
            '',
            $form->projectId,
            $form->typeCreate,
            $parentId,
            $form->ip
        );
        $client->id = 0;

        return $client;
    }

    /**
     * Find or create Client
     *
     * @param EmailCreateForm[] $emails
     * @param ClientCreateForm $clientForm
     * @return Client
     */
    public function getOrCreateByEmails(array $emails, ClientCreateForm $clientForm): Client
    {
        $emails = $this->guardNotEmptyEmails($emails);

        $parentId = null;
        $projectId = (int)$clientForm->projectId;

        foreach ($emails as $email) {
            $collections = new ClientsCollection(ClientsQuery::allByEmail($email->email));
            if ($collections->isEmpty()) {
                continue;
            }
            if ($projectId) {
                if ($client = $collections->getWithProject($projectId)) {
                    return $client;
                }
            } else {
                if ($client = $collections->getWithoutProject()) {
                    return $client;
                }
            }
            $parentId = $collections->getFirstId();
            break;
        }

        $client = $this->create($clientForm, $parentId);
        $this->addEmails($client, $emails);

        if (!$client->clientEmails) {
            throw new \DomainException('Cannot create client. Not added emails.', ClientCodeException::CLIENT_CREATE_NOT_ADD_EMAILS);
        }

        return $client;
    }

    public function getOrCreateByClientId(ClientCreateForm $form, $parentId): Client
    {
        if ($client = Client::findOne($form->id)) {
            return $client;
        }
        return $this->create($form, $parentId);
    }

    public function createByRcId(ClientCreateForm $form, $parentId): Client
    {
        $client = Client::create(
            $form->firstName,
            $form->middleName,
            $form->lastName,
            $form->projectId,
            $form->typeCreate,
            $parentId,
            $form->ip
        );
        $client->uuid = $form->uuid;

        $this->clientRepository->save($client);

        return $client;
    }

    public function getOrCreateByClientChatRequest(ClientChatRequest $clientChatRequest, int $projectId): Client
    {
        $clientEmailForm = (new EmailCreateForm());
        $clientEmailForm->email = $clientChatRequest->getEmailFromData();

        $clientPhoneForm = new PhoneCreateForm();
        $clientPhoneForm->phone = $clientChatRequest->getPhoneFromData();

        $rcId = $clientChatRequest->getClientRcId();
        $uuId = $clientChatRequest->getClientUuId();

        if (empty($rcId)) {
            throw new \RuntimeException('Client Rocket Chat id is not provided');
        }

        $ip = null;
        if ($data = $clientChatRequest->getDecodedData()) {
            $ip = $data['geo']['ip'] ?? null;
        }
        $clientForm = new ClientCreateForm([
            'firstName' => $clientChatRequest->getNameFromData(),
            'rcId' => $rcId,
            'uuid' => $uuId,
            'typeCreate' => Client::TYPE_CREATE_CLIENT_CHAT,
            'projectId' => $projectId,
            'ip' => $ip,
        ]);

        if ($client = $this->detectClientFromChatRequest($projectId, $uuId, $clientEmailForm->email, $rcId)) {
            $this->updateClient($client, $clientForm);
        } else {
            if (empty($clientForm->projectId)) {
                throw new \RuntimeException('Cannot create client without Project');
            }

            $parentId = null;
            if ($parent = ClientsQuery::findParentByEmail($clientEmailForm->email, $clientForm->projectId)) {
                /** @var Client $parent */
                $parentId = $parent->id;
            }
            $client = $this->createByRcId($clientForm, $parentId);
        }

        $this->addEmail($client, $clientEmailForm);
        $this->addPhone($client, $clientPhoneForm);

        return $client;
    }

    /**
     * @param int $projectId
     * @param string|null $uuId
     * @param string|null $email
     * @param string|null $rcId
     * @return Client|ActiveRecord|null
     */
    public function detectClientFromChatRequest(int $projectId, ?string $uuId, ?string $email, ?string $rcId)
    {
        if (!empty($uuId) && $client = Client::find()->byProject($projectId)->byUuid($uuId)->one()) {
            return $client;
        }
        if (!empty($email) && $client = ClientsQuery::oneByEmailAndProject($email, $projectId)) {
            return $client;
        }
        if (!empty($rcId) && $client = Client::find()->byProject($projectId)->joinWithCcVisitor($rcId)->one()) {
            return $client;
        }
        return null;
    }

    /**
     * Find or create Client
     *
     * @param PhoneCreateForm[] $phones
     * @param EmailCreateForm[] $emails
     * @param ClientCreateForm $clientForm
     * @param string|null $uuid
     * @return Client
     */
    public function getOrCreate(array $phones, array $emails, ClientCreateForm $clientForm, ?string $uuid = null): Client
    {
        if (!(!empty($uuid) && $client = Client::findOne(['uuid' => $uuid]))) {
            try {
                $client = $this->getOrCreateByPhones($phones, $clientForm);
            } catch (InternalPhoneException $e) {
                throw $e;
            } catch (\DomainException $e) {
                $client = $this->getOrCreateByEmails($emails, $clientForm);
            }
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

    /**
     * @param Client $client
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function removeContact(Client $client): void
    {
        $client = $this->finder->clientFind($client);
        $client->cl_type_id = Client::TYPE_CLIENT;
        $clientId = $this->clientRepository->save($client);

        if ($userContactList = UserContactList::findOne(['ucl_client_id' => $clientId])) {
            $userContactList->delete();
        }
    }

    public function createOrLinkByClientAccount(ClientAccount $clientAccount): Client
    {
        if ($client = Client::findOne(['uuid' => $clientAccount->ca_uuid])) {
            $client->cl_ca_id = $clientAccount->ca_id;
        } else {
            $client = Client::createByClientAccount($clientAccount);
        }
        $this->clientRepository->save($client);

        if ($clientAccount->ca_email) {
            $clientEmailForm = new EmailCreateForm();
            $clientEmailForm->email = $clientAccount->ca_email;
            $this->addEmail($client, $clientEmailForm);
        }

        if ($clientAccount->ca_phone) {
            $clientPhoneForm = new PhoneCreateForm();
            $clientPhoneForm->phone = $clientAccount->ca_phone;
            $this->addPhone($client, $clientPhoneForm);
        }

        return $client;
    }

    /**
     * @param Client $client
     * @param $saleData
     * @return string|null
     * @throws InvalidConfigException
     */
    public static function setLocaleFromSaleDate(Client $client, $saleData): ?string
    {
        $result = null;
        $locale = $saleData['user_language'] ?? null;
        if ($locale && empty($client->cl_locale)) {
            $client->cl_locale = $locale;
            if ($client->validate('cl_locale')) {
                $clientRepository = \Yii::createObject(ClientRepository::class);
                $clientRepository->save($client);
                $result = $locale;
            } else {
                throw new \RuntimeException('Client locale is not valid. ' .
                    $client->getFirstError('cl_locale'), -1);
            }
        }
        return $result;
    }

    /**
     * @param Client $client
     * @param $saleData
     * @return string|null
     * @throws InvalidConfigException
     */
    public static function setMarketingCountryFromSaleDate(Client $client, $saleData): ?string
    {
        $result = null;
        $country = $saleData['user_country'] ?? null;
        if ($country && empty($client->cl_marketing_country)) {
            $client->cl_marketing_country = $country;
            if ($client->validate('cl_marketing_country')) {
                $clientRepository = \Yii::createObject(ClientRepository::class);
                $clientRepository->save($client);
                $result = strtoupper($client->cl_marketing_country);
            } else {
                throw new \RuntimeException('Client market country is not valid. ' .
                    $client->getFirstError('cl_marketing_country'), -1);
            }
        }
        return $result;
    }
}
