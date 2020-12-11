<?php

namespace sales\model\lead\useCases\lead\api\create;

use common\models\Client;
use common\models\Lead;
use common\models\LeadFlightSegment;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\lead\calculator\LeadTripTypeCalculator;
use sales\services\lead\calculator\SegmentDTO;
use sales\services\lead\LeadHashGenerator;
use sales\services\TransactionManager;

/**
 * Class Handler
 *
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transactionManager
 * @property LeadHashGenerator $hashGenerator
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $segmentRepository
 */
class LeadCreateHandler
{
    private $clientManageService;
    private $transactionManager;
    private $hashGenerator;
    private $leadRepository;
    private $segmentRepository;

    public function __construct(
        ClientManageService $clientManageService,
        TransactionManager $transactionManager,
        LeadHashGenerator $hashGenerator,
        LeadRepository $leadRepository,
        LeadSegmentRepository $segmentRepository
    ) {
        $this->clientManageService = $clientManageService;
        $this->transactionManager = $transactionManager;
        $this->hashGenerator = $hashGenerator;
        $this->leadRepository = $leadRepository;
        $this->segmentRepository = $segmentRepository;
    }

    public function handle(LeadCreateForm $form): Lead
    {
        $lead = $this->transactionManager->wrap(function () use ($form) {

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $form->project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_LEAD;
            $clientForm->ip = $form->request_ip;

            $client = $this->clientManageService->getOrCreate(
                [new PhoneCreateForm(['phone' => $form->clientForm->phone])],
                [new EmailCreateForm(['email' => $form->clientForm->email])],
                $clientForm,
                $form->clientForm->uuid
            );

            $lead = Lead::createByApiBO($form, $client);

            $hash = $this->hashGenerator->generate(
                $form->request_ip,
                $form->project_id,
                $form->adults,
                $form->children,
                $form->infants,
                $form->cabin,
                [$form->clientForm->phone],
                $this->getSegments($form->flightsForm)
            );

            $lead->setRequestHash($hash);

            if ($duplicate = $this->leadRepository->getByRequestHash($hash)) {
                $lead->status = null;
                $lead->duplicate($duplicate->id, null, null);
            } else {
                $lead->eventLeadCreatedByApiBOEvent();
            }

            $lead->setTripType($this->calculateTripType($form->flightsForm));

            $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$form->clientForm->phone]);

            $leadId = $this->leadRepository->save($lead);

            $this->createFlightSegments($leadId, $form->flightsForm);

            return $lead;
        });

        return $lead;
    }

    /**
     * @param int $leadId
     * @param FlightForm[] $flights
     */
    private function createFlightSegments(int $leadId, array $flights): void
    {
        foreach ($flights as $item) {
            $segment = LeadFlightSegment::create(
                $leadId,
                $item->origin,
                $item->destination,
                $item->departure,
                0,
                '+/-'
            );
            $this->segmentRepository->save($segment);
        }
    }

    /**
     * @param FlightForm[] $flightsForm
     * @return array
     */
    private function getSegments(array $flightsForm): array
    {
        $segments = [];
        foreach ($flightsForm as $segmentForm) {
            $segments[] = [
                'origin' => $segmentForm->origin,
                'destination' => $segmentForm->destination,
                'departure' => $segmentForm->departure,
            ];
        }
        return $segments;
    }

    /**
     * @param FlightForm[] $flights
     * @return string
     */
    private function calculateTripType(array $flights): string
    {
        $segmentsDTO = [];
        foreach ($flights as $segment) {
            $segmentsDTO[] = new SegmentDTO($segment->origin, $segment->destination);
        }

        return LeadTripTypeCalculator::calculate(...$segmentsDTO);
    }
}
