<?php

namespace src\services\lead;

use common\models\Client;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\visitorLog\useCase\CreateVisitorLog;
use src\services\client\ClientCreateForm;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use common\models\Sources;
use common\models\VisitorLog;
use src\forms\lead\ItineraryEditForm;
use src\forms\lead\LeadCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\forms\lead\PreferencesCreateForm;
use src\forms\lead\SegmentCreateForm;
use src\forms\lead\SegmentEditForm;
use src\forms\lead\SegmentForm;
use src\repositories\airport\AirportRepository;
use src\repositories\cases\CasesRepository;
use src\repositories\client\ClientEmailRepository;
use src\repositories\client\ClientPhoneRepository;
use src\repositories\client\ClientRepository;
use src\repositories\lead\LeadPreferencesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\lead\LeadSegmentRepository;
use src\services\cases\CasesManageService;
use src\services\client\ClientManageService;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use src\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $segmentRepository
 * @property ClientEmailRepository $clientEmailRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property ClientRepository $clientRepository
 * @property LeadHashGenerator $leadHashGenerator
 * @property AirportRepository $airportRepository
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 * @property CasesManageService $casesManageService
 * @property TransactionManager $transaction
 */
class LeadManageService
{
    private $leadRepository;
    private $segmentRepository;
    private $clientEmailRepository;
    private $clientPhoneRepository;
    private $leadPreferencesRepository;
    private $clientRepository;
    private $leadHashGenerator;
    private $airportRepository;
    private $clientManageService;
    private $casesRepository;
    private $casesManageService;
    private $transaction;

    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $segmentRepository,
        ClientEmailRepository $clientEmailRepository,
        ClientPhoneRepository $clientPhoneRepository,
        LeadPreferencesRepository $leadPreferencesRepository,
        ClientRepository $clientRepository,
        LeadHashGenerator $leadHashGenerator,
        AirportRepository $airportRepository,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        CasesManageService $casesManageService,
        TransactionManager $transaction
    ) {
        $this->leadRepository = $leadRepository;
        $this->segmentRepository = $segmentRepository;
        $this->clientEmailRepository = $clientEmailRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->clientRepository = $clientRepository;
        $this->leadHashGenerator = $leadHashGenerator;
        $this->airportRepository = $airportRepository;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->casesManageService = $casesManageService;
        $this->transaction = $transaction;
    }

    /**
     * @param string $clientEmail
     * @param int $clientId
     * @param int|null $projectId
     * @param int|null $sourceId
     * @param int $departmentId
     * @return Lead
     */
    public function createByIncomingEmail(
        string $clientEmail,
        int $clientId,
        ?int $projectId,
        ?int $sourceId,
        int $departmentId
    ): Lead {
        $lead = Lead::createByIncomingEmail($clientEmail, $clientId, $projectId, $sourceId, $departmentId);

        $this->leadRepository->save($lead);

        return $lead;
    }

    public function createByIncomingSms(
        string $clientPhone,
        int $clientId,
        ?int $projectId,
        ?int $sourceId,
        int $departmentId
    ): Lead {
        $lead = Lead::createByIncomingSms($clientPhone, $clientId, $projectId, $sourceId, $departmentId);

        $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$clientPhone]);

        $this->leadRepository->save($lead);

        return $lead;
    }

    /**
     * @param string $phoneNumber
     * @param int|null $projectId
     * @param int|null $sourceId
     * @param int $departmentId
     * @param $gmt
     * @param int $callId
     * @return Lead
     * @throws \Throwable
     */
    public function createByIncomingCall(
        string $phoneNumber,
        ?int $projectId,
        ?int $sourceId,
        int $departmentId,
        $gmt,
        int $callId
    ): Lead {
        $lead = $this->transaction->wrap(function () use ($phoneNumber, $projectId, $sourceId, $departmentId, $gmt, $callId) {

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $projectId;
            $clientForm->typeCreate = Client::TYPE_CREATE_CALL;
            $client = $this->clientManageService->getOrCreateByPhones([new PhoneCreateForm(['phone' => $phoneNumber, 'comments' => 'incoming'])], $clientForm);

            $sourceId = $this->getSourceId($sourceId, $projectId);

            $lead = Lead::createByIncomingCall($phoneNumber, $client->id, $projectId, $sourceId, $departmentId, $gmt);

            $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$phoneNumber]);

            $this->leadRepository->save($lead);

            if ($logId = (new CreateVisitorLog())->create($client, $lead)) {
                $lead->setVisitorLog($logId);
                $this->leadRepository->save($lead);
            }

            LeadDataCreateService::createByCallId($lead->id, $callId);

            return $lead;
        });

        return $lead;
    }

    /**
     * @param int|null $sourceId
     * @param int|null $projectId
     * @return int|null
     */
    private function getSourceId(?int $sourceId, ?int $projectId): ?int
    {
        if ($sourceId && ($source = Sources::findOne(['id' => $sourceId]))) {
            return $source->id;
        }

        if ($projectId && $source = Sources::find()->select('id')->where(['project_id' => $projectId, 'default' => true])->one()) {
            return $source->id;
        }

        return $sourceId;
    }

    /**
     * @param LeadCreateForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Exception
     */
    public function createManuallyByDefault(LeadCreateForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {

        $lead = $this->transaction->wrap(function () use ($form, $employeeId, $creatorId, $reason) {

            return $this->createManually($form, $employeeId, $creatorId, $reason);
        });

        return $lead;
    }

    /**
     * @param LeadCreateForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Exception
     */
    public function createManuallyFromCase(LeadCreateForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {

        $lead = $this->transaction->wrap(function () use ($form, $employeeId, $creatorId, $reason) {

            $case = $this->casesRepository->findFreeByGid($form->caseGid);

            $lead = $this->createManually($form, $employeeId, $creatorId, $reason);

            $this->casesManageService->assignLead($case->cs_id, $lead->id);

            return $lead;
        });

        return $lead;
    }

    /**
     * @param LeadCreateForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     */
    private function createManually(
        LeadCreateForm $form,
        int $employeeId,
        ?int $creatorId,
        ?string $reason
    ): Lead {
        $client = $this->clientManageService->getOrCreate($form->phones, $form->emails, $form->client);

        $lead = Lead::createManually(
            $client->id,
            $form->client->firstName,
            $form->client->lastName,
            $form->cabin,
            $form->adults,
            $form->children,
            $form->infants,
            $form->requestIp,
            $form->sourceId,
            $form->projectId,
            $form->notesForExperts,
            $form->clientPhone,
            $form->clientEmail,
            $form->depId,
            $form->delayedCharge,
            Lead::TYPE_CREATE_MANUALLY
        );

        $lead->processing($employeeId, $creatorId, $reason);

        $phones = [];
        foreach ($form->phones as $phone) {
            $phones[] = $phone->phone;
        }

        $segments = $this->getSegments($form->segments);

        $hash = $this->leadHashGenerator->generate(
            $form->requestIp,
            $form->projectId,
            $form->adults,
            $form->children,
            $form->infants,
            $form->cabin,
            $phones,
            $segments
        );

        $lead->setRequestHash($hash);

//        if ($origin = $this->leadRepository->getByRequestHash($lead->l_request_hash)) {
//            $lead->duplicate($origin->id, $employeeId, $creatorId);
//        }

        $lead->setTripType($this->calculateTripType($form->segments));

        $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest($phones);

        $leadId = $this->leadRepository->save($lead);

        $this->createFlightSegments($leadId, $form->segments);

        $this->createLeadPreferences($leadId, $form->preferences);

        if ($logId = (new CreateVisitorLog())->create($client, $lead)) {
            $lead->setVisitorLog($logId);
            $this->leadRepository->save($lead);
        }

        return $lead;
    }

    /**
     * @param Lead $lead
     * @param ItineraryEditForm $form
     * @throws \Throwable
     */
    public function editItinerary(Lead $lead, ItineraryEditForm $form): void
    {
//        $lead = $this->leadRepository->find($id);

        $lead->editItinerary(
            $form->cabin,
            $form->adults,
            $form->children,
            $form->infants
        );

        $this->transaction->wrap(function () use ($lead, $form) {

            $lead->setTripType(self::calculateTripType($form->segments));
            $newSegmentsIds = [];
            foreach ($form->segments as $segmentForm) {
                $segment = $this->getSegment($lead->id, $segmentForm);
                $newSegmentsIds[] = $this->segmentRepository->save($segment);
            }
            $this->segmentRepository->removeOld($lead->leadFlightSegments, $newSegmentsIds);

            $this->leadRepository->save($lead);

            $description = LeadPoorProcessingLogStatus::REASON_CHANGE_ITINERARY;
            $lead->updateLastAction($description);
        });
    }

    /**
     * @param SegmentCreateForm[] $segmentsForm
     * @return array
     */
    private function getSegments(array $segmentsForm): array
    {
        $segments = [];
        foreach ($segmentsForm as $segmentForm) {
            $segments[] = [
                'origin' => $segmentForm->origin,
                'destination' => $segmentForm->destination,
                'departure' => $segmentForm->departure,
            ];
        }
        return $segments;
    }

    /**
     * @param int $leadId
     * @param PreferencesCreateForm $preferencesForm
     */
    private function createLeadPreferences(int $leadId, PreferencesCreateForm $preferencesForm): void
    {
        $preferences = LeadPreferences::create(
            $leadId,
            $preferencesForm->marketPrice,
            $preferencesForm->clientsBudget,
            $preferencesForm->numberStops,
            $preferencesForm->currency
        );
        $preferences->setDefaultCurrencyCodeIfNotSet(false);
        $this->leadPreferencesRepository->save($preferences);
    }

    /**
     * @param int $leadId
     * @param SegmentCreateForm[] $segmentsForm
     */
    private function createFlightSegments(int $leadId, array $segmentsForm): void
    {
        foreach ($segmentsForm as $segmentForm) {
            $segment = LeadFlightSegment::create(
                $leadId,
                $segmentForm->origin,
                $segmentForm->destination,
                $segmentForm->departure,
                $segmentForm->flexibility,
                $segmentForm->flexibilityType
            );
            $this->segmentRepository->save($segment);
        }
    }

    /**
     * @param array $segments
     * @return string
     */
    private function calculateTripType(array $segments): string
    {
        $segmentsDTO = [];

        /** @var SegmentForm $segment */
        foreach ($segments as $segment) {
            $segmentsDTO[] = new SegmentDTO($segment->origin, $segment->destination);
        }

        return LeadTripTypeCalculator::calculate(...$segmentsDTO);
    }

    /**
     * @param int $leadId
     * @param SegmentEditForm $segmentForm
     * @return LeadFlightSegment
     */
    private function getSegment(int $leadId, SegmentEditForm $segmentForm): LeadFlightSegment
    {
        if ($segmentForm->segmentId) {
            $segment = $this->segmentRepository->find($segmentForm->segmentId);
            $segment->edit(
                $segmentForm->origin,
                $segmentForm->destination,
                $segmentForm->departure,
                $segmentForm->flexibility,
                $segmentForm->flexibilityType
            );
            return $segment;
        }
        $segment = LeadFlightSegment::create(
            $leadId,
            $segmentForm->origin,
            $segmentForm->destination,
            $segmentForm->departure,
            $segmentForm->flexibility,
            $segmentForm->flexibilityType
        );
        return $segment;
    }
}
