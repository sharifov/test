<?php

namespace sales\services\lead;

use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\LeadCreateForm;
use sales\forms\lead\PreferencesCreateForm;
use sales\forms\lead\SegmentCreateForm;
use sales\forms\lead\SegmentEditForm;
use sales\forms\lead\SegmentForm;
use sales\repositories\airport\AirportRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientManageService;
use sales\services\lead\calculator\LeadTripTypeCalculator;
use sales\services\lead\calculator\SegmentDTO;
use sales\services\TransactionManager;
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
        TransactionManager $transaction)
    {
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
     * @param LeadCreateForm $form
     * @param int $employeeId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Exception
     */
    public function create(LeadCreateForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {

        $lead = $this->transaction->wrap(function () use ($form, $employeeId, $creatorId, $reason) {

           return $this->createLead($form, $employeeId, $creatorId, $reason);

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
    public function createWithCase(LeadCreateForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {

        $lead = $this->transaction->wrap(function () use ($form, $employeeId, $creatorId, $reason) {

            $case = $this->casesRepository->findFreeByGid($form->caseGid);

            $lead = $this->createLead($form, $employeeId, $creatorId, $reason);

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
    private function createLead(LeadCreateForm $form, int $employeeId, ?int $creatorId = null, ?string $reason = ''): Lead
    {

        $client = $this->clientManageService->getOrCreate($form->phones, $form->emails, $form->client);

        $lead = Lead::create(
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
            $form->delayedCharge
        );

        $lead->processing($employeeId, $creatorId, $reason);

        $this->clientManageService->addPhones($client, $form->phones);

        $phones = [];
        foreach ($form->phones as $phone) {
            $phones[] = $phone->phone;
        }

        $this->clientManageService->addEmails($client, $form->emails);

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

        if ($origin = $this->leadRepository->getByRequestHash($lead->l_request_hash)) {
            $lead->duplicate($origin->id, $employeeId, $creatorId);
        }

        $lead->setTripType($this->calculateTripType($form->segments));

        $leadId = $this->leadRepository->save($lead);

        $this->createFlightSegments($leadId, $form->segments);

        $this->createLeadPreferences($leadId, $form->preferences);

        return $lead;

    }

    /**
     * @param $id
     * @param ItineraryEditForm $form
     * @throws \Exception
     */
    public function editItinerary(int $id, ItineraryEditForm $form): void
    {
        $lead = $this->leadRepository->find($id);

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
            $preferencesForm->numberStops
        );
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
