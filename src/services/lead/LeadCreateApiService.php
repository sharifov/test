<?php

namespace src\services\lead;

use common\components\jobs\WebEngageLeadRequestJob;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\VisitorLog;
use modules\webEngage\settings\WebEngageDictionary;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\repositories\client\ClientsCollection;
use src\repositories\client\ClientsQuery;
use src\repositories\lead\LeadPreferencesRepository;
use src\repositories\lead\LeadSegmentRepository;
use src\services\TransactionManager;
use Yii;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use src\services\client\ClientCreateForm;
use src\repositories\lead\LeadRepository;
use src\services\client\ClientManageService;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use webapi\models\ApiLead;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\UnprocessableEntityHttpException;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class LeadCreateApiService
 *
 * @property ClientManageService $clientManageService
 * @property LeadHashGenerator $leadHashGenerator
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $leadSegmentRepository
 * @property TransactionManager $transactionManager
 * @property LeadPreferencesRepository $leadPreferencesRepository
 */
class LeadCreateApiService
{
    private $clientManageService;
    private $leadHashGenerator;
    private $leadRepository;
    private $leadSegmentRepository;
    private $transactionManager;
    private $leadPreferencesRepository;

    public function __construct(
        LeadRepository $leadRepository,
        ClientManageService $clientManageService,
        LeadHashGenerator $leadHashGenerator,
        LeadSegmentRepository $leadSegmentRepository,
        TransactionManager $transactionManager,
        LeadPreferencesRepository $leadPreferencesRepository
    ) {
        $this->leadRepository = $leadRepository;
        $this->clientManageService = $clientManageService;
        $this->leadHashGenerator = $leadHashGenerator;
        $this->leadSegmentRepository = $leadSegmentRepository;
        $this->transactionManager = $transactionManager;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
    }

    /**
     * @param ApiLead $modelLead
     * @param $apiProject
     * @return Lead
     * @throws \Throwable
     */
    public function createByApi(ApiLead $modelLead, $apiProject): Lead
    {
        /** @var Lead $lead */
        $lead = $this->transactionManager->wrap(function () use ($modelLead, $apiProject) {

            $lead = Lead::createByApi();
            $lead->l_client_email = $this->getRequestEmail($modelLead->emails);
            $lead->l_client_phone = $this->getRequestPhone($modelLead->phones);

            $client = $this->findOrCreateClient($modelLead, $lead);
            $this->addClientPhones($client, $modelLead->phones);
            $this->addClientEmails($client, $modelLead->emails);

            $lead->attributes = $modelLead->attributes;
            $lead->client_id = $client->id;

            if (!$lead->status) {
                $lead->status = Lead::STATUS_PENDING;
            }
            if (!$lead->uid) {
                $lead->uid = Lead::generateUid();
            }
            if (!$lead->cabin) {
                $lead->cabin = Lead::CABIN_ECONOMY;
            }
            if (!$lead->children) {
                $lead->children = 0;
            }
            if (!$lead->infants) {
                $lead->infants = 0;
            }
            if (!$lead->request_ip) {
                $lead->request_ip = Yii::$app->request->remoteIP;
            }
            if ($apiProject) {
                $lead->project_id = $apiProject->id;
            }

            if (!$lead->l_client_lang && $modelLead->user_language) {
                $lead->l_client_lang = $modelLead->user_language;
            }
            if (!$lead->l_client_ua && $modelLead->user_agent) {
                $lead->l_client_ua = $modelLead->user_agent;
            }
            if (!$lead->l_client_first_name && $modelLead->client_first_name) {
                $lead->l_client_first_name = $modelLead->client_first_name;
            }
            if (!$lead->l_client_last_name && $modelLead->client_last_name) {
                $lead->l_client_last_name = $modelLead->client_last_name;
            }
            if (!$lead->l_expiration_dt && $modelLead->expire_at) {
                $lead->l_expiration_dt = $modelLead->expire_at;
            }

            $lead->l_call_status_id = Lead::CALL_STATUS_READY;

            $request_hash = $this->leadHashGenerator->generate(
                $modelLead->request_ip,
                $modelLead->project_id,
                $modelLead->adults,
                $modelLead->children,
                $modelLead->infants,
                $modelLead->cabin,
                $modelLead->phones,
                $modelLead->flights
            );

            if ($duplicate = $this->leadRepository->getByRequestHash($request_hash)) {
                $lead->status = null;
                $lead->duplicate($duplicate->id, $modelLead->employee_id, null);
            } else {
                $lead->eventLeadCreatedByApiEvent();
            }

            if ($request_hash && $lead->isEmptyRequestHash()) {
                $lead->setRequestHash($request_hash);
            }

            if (!$lead->trip_type) {
                $lead->trip_type = Lead::TRIP_TYPE_ROUND_TRIP;
            }

            $this->calculateTripType($modelLead->flights, $lead);

            $lead->l_is_test =  $modelLead->is_test ? 1 : $this->clientManageService->checkIfPhoneIsTest($modelLead->phones);

            if (!$lead->validate()) {
                if ($errors = $lead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 7);
                } else {
                    throw new UnprocessableEntityHttpException('Not validate Lead data', 7);
                }
            }

            try {
                $this->leadRepository->save($lead);
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'API:LeadCreateApiService:createByApi:lead:save');
                throw new UnprocessableEntityHttpException($e->getMessage(), 8);
            }

            $this->addFlights($modelLead->flights, $lead->id);

            $leadPreferences = LeadPreferences::create($lead->id, null, null, null, $modelLead->currency_code);
            $this->leadPreferencesRepository->save($leadPreferences);

            return $lead;
        });

        $this->addVisitorLogs($modelLead, $lead);

        return $lead;
    }

    private function addVisitorLogs(ApiLead $modelLead, Lead $lead): void
    {
        $lastLogId = null;
        $lastVisitDt = null;
        foreach ($modelLead->visitor_log as $visitorLog) {
            $log = new VisitorLog([
                'vl_project_id' => $lead->project_id,
                'vl_client_id' => $lead->client_id,
                'vl_lead_id' => $lead->id,
            ]);
            if ($log->load($visitorLog, '')) {
                if (!$log->save()) {
                    Yii::error(
                        'Cant save visitor_log. LeadId: ' . $lead->id
                        . ' log: ' . VarDumper::dumpAsString($visitorLog)
                        . ' Errors: ' . VarDumper::dumpAsString($log->getErrors()),
                        'API:LeadCreateApiService:createByApi:visitor:log:save'
                    );
                } else {
                    if ($lastVisitDt) {
                        if (strtotime($log->vl_visit_dt) >= strtotime($lastVisitDt)) {
                            $lastLogId = $log->vl_id;
                            $lastVisitDt = $log->vl_visit_dt;
                        }
                    } else {
                        $lastLogId = $log->vl_id;
                        $lastVisitDt = $log->vl_visit_dt;
                    }
                }
            } else {
                Yii::error('Cant load visitor_log. LeadId: ' . $lead->id . ' log: ' . VarDumper::dumpAsString($visitorLog), 'API:LeadCreateApiService:createByApi:visitor:log:load');
            }
        }

        if ($lastLogId) {
            try {
                $lead->setVisitorLog($lastLogId);
                $this->leadRepository->save($lead);
            } catch (\Throwable $e) {
                Yii::error('Cant update lead. ' . $e, 'API:LeadCreateApiService:createByApi:lead:update:visitor_log');
            }
        }
    }

    /**
     * @param $phones
     * @param Lead $lead
     * @return Client|null
     */
    private function getClientByPhones($phones, Lead $lead): ?Client
    {
        $client = null;
        if ($phones) {
            foreach ($phones as $phone) {
                $phone = trim($phone);
                if (!$phone) {
                    continue;
                }
                $phoneModel = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                if ($phoneModel && $phoneModel->client) {
                    $lead->l_client_phone = $phone;
                    $client = $phoneModel->client;
                    break;
                }
            }
        }
        return $client;
    }

    /**
     * @param $phones
     * @return string|null
     */
    private function getRequestPhone($phones): ?string
    {
        $clientPhone = null;
        if ($phones) {
            foreach ($phones as $phone) {
                $phone = trim($phone);
                if (!$phone) {
                    continue;
                }
                $clientPhone = $phone;
            }
        }
        return $clientPhone;
    }

    /**
     * @param $emails
     * @return string|null
     */
    private function getRequestEmail($emails): ?string
    {
        $clientEmail = null;
        if ($emails) {
            foreach ($emails as $email) {
                $email = mb_strtolower(trim($email));
                if (!$email) {
                    continue;
                }
                $clientEmail = $email;
            }
        }
        return $clientEmail;
    }

    /**
     * @param ApiLead $modelLead
     * @param Lead $lead
     * @return Client
     * @throws UnprocessableEntityHttpException
     */
    private function findOrCreateClient(ApiLead $modelLead, Lead $lead): Client
    {
        $projectId = $modelLead->project_id;
        $parentId = null;

        if (!empty($modelLead->clientUuid) && $client = Client::findOne(['uuid' => $modelLead->clientUuid])) {
            return $client;
        }

        if (!is_array($modelLead->phones)) {
            throw new UnprocessableEntityHttpException('Phones from ModelLead cannot be empty.');
        }

        foreach ($modelLead->phones as $phone) {
            $phone = trim($phone);
            if (!$phone) {
                continue;
            }
            $collections = new ClientsCollection(ClientsQuery::allByPhone($phone));
            if ($collections->isEmpty()) {
                continue;
            }
            if ($projectId) {
                if ($client = $collections->getWithProject($projectId)) {
                    $lead->l_client_phone = $phone;
                    return $client;
                }
            } else {
                if ($client = $collections->getWithoutProject()) {
                    $lead->l_client_phone = $phone;
                    return $client;
                }
            }
            $parentId = $collections->getFirstId();
            break;
        }

        $firstName = null;
        $lastName = null;
        $middleName = null;

        if ($modelLead->client_first_name) {
            $firstName = $modelLead->client_first_name;
        } else {
            $firstName = 'ClientName';
        }

        if ($modelLead->client_last_name) {
            $lastName = $modelLead->client_last_name;
        }
        if ($modelLead->client_middle_name) {
            $middleName = $modelLead->client_middle_name;
        }

        $newClient = new ClientCreateForm([
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'projectId' => $modelLead->project_id,
            'typeCreate' => Client::TYPE_CREATE_LEAD,
            'ip' => $modelLead->request_ip,
        ]);

        if (!$newClient->validate()) {
            throw new UnprocessableEntityHttpException($this->errorToString($newClient->errors));
        }

        try {
            $client = $this->clientManageService->create($newClient, $parentId);
        } catch (\Throwable $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        return $client;
    }

    /**
     * @param Client $client
     * @param $emails
     * @throws UnprocessableEntityHttpException
     */
    private function addClientEmails(Client $client, $emails): void
    {
        if ($emails) {
            foreach ($emails as $email) {
                $email = mb_strtolower(trim($email));
                $newEmail = new EmailCreateForm(['email' => $email, 'type' => ClientEmail::EMAIL_NOT_SET]);
                if (!$newEmail->validate()) {
                    Yii::error(print_r($newEmail->errors, true), 'API:LeadCreateApiService:addClientEmails:validate');
                    throw new UnprocessableEntityHttpException($this->errorToString($newEmail->errors), 11);
                }
                try {
                    $this->clientManageService->addEmail($client, $newEmail);
                } catch (\Throwable $e) {
                    Yii::error($e->getMessage(), 'API:LeadCreateApiService:addClientEmails:save');
                    throw new UnprocessableEntityHttpException($e->getMessage(), 11);
                }
            }
        }
    }

    /**
     * @param Client $client
     * @param $phones
     * @throws UnprocessableEntityHttpException
     */
    private function addClientPhones(Client $client, $phones): void
    {
        if ($phones) {
            foreach ($phones as $phone) {
                $phone = trim($phone);
                $newPhone = new PhoneCreateForm(['phone' => $phone, 'type' => ClientPhone::PHONE_NOT_SET]);
                if (!$newPhone->validate()) {
                    Yii::error(print_r($newPhone->errors, true), 'API:LeadCreateApiService:addClientPhones:validate');
                    throw new UnprocessableEntityHttpException($this->errorToString($newPhone->errors), 12);
                }
                try {
                    $this->clientManageService->addPhone($client, $newPhone);
                } catch (\Throwable $e) {
                    Yii::error($e->getMessage(), 'API:LeadCreateApiService:addClientPhones:save');
                    throw new UnprocessableEntityHttpException($e->getMessage(), 12);
                }
            }
        }
    }

    /**
     * @param $flights
     * @param Lead $lead
     */
    private function calculateTripType($flights, Lead $lead): void
    {
        if ($flights) {
            $segmentsDTO = [];
            foreach ($flights as $flight) {
                $segmentsDTO[] = new SegmentDTO($flight['origin'], $flight['destination']);
            }
            $lead->setTripType(LeadTripTypeCalculator::calculate(...$segmentsDTO));
        }
    }

    /**
     * @param $flights
     * @param int $leadId
     * @throws UnprocessableEntityHttpException
     */
    private function addFlights($flights, int $leadId): void
    {
        if ($flights) {
            foreach ($flights as $flight) {
                $flightModel = new LeadFlightSegment();
                $flightModel->scenario = LeadFlightSegment::SCENARIO_CREATE_API;
                $flightModel->lead_id = $leadId;
                $flightModel->origin = $flight['origin'];
                $flightModel->destination = $flight['destination'];
                $flightModel->departure = $flight['departure'];
                if (!$flightModel->validate()) {
                    Yii::error(print_r($flightModel->errors, true), 'API:LeadCreateApiService:LeadFlightSegment:validate');
                    throw new UnprocessableEntityHttpException($this->errorToString($flightModel->errors), 10);
                }
                try {
                    $this->leadSegmentRepository->save($flightModel);
                } catch (\Throwable $e) {
                    Yii::error($e->getMessage(), 'API:LeadCreateApiService:LeadFlightSegment:save');
                    throw new UnprocessableEntityHttpException($e->getMessage(), 10);
                }
            }
        }
    }

    /**
     * @param array $errors
     * @return string
     */
    private function errorToString($errors = []): string
    {
        $arr_errors = [];
        foreach ($errors as $k => $v) {
            $arr_errors[] = is_array($v) ? implode(',', $v) : print_r($v, true);
        }
        return implode('; ', $arr_errors);
    }
}
