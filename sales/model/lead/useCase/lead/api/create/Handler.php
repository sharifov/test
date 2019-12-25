<?php

namespace sales\model\lead\useCase\lead\api\create;

use common\models\Lead;
use common\models\LeadFlightSegment;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
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
class Handler
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
    )
    {
        $this->clientManageService = $clientManageService;
        $this->transactionManager = $transactionManager;
        $this->hashGenerator = $hashGenerator;
        $this->leadRepository = $leadRepository;
        $this->segmentRepository = $segmentRepository;
    }

    public function handle(LeadForm $form)
    {
        $this->transactionManager->wrap(function () use ($form) {

            $client = $this->clientManageService->getOrCreateByPhones([new PhoneCreateForm(['phone' => $form->phone])]);

            $lead = Lead::createByApiBO($form, $client);

            $hash = $this->hashGenerator->generate(
                $form->request_ip,
                $form->project_id,
                $form->adults,
                $form->children,
                $form->infants,
                $form->cabin,
                [$form->phone],
                $this->getSegments($form->segments)
            );

            $lead->setRequestHash($hash);

            $lead->setTripType($this->calculateTripType($form->segments));

            $lead->l_is_test = $this->clientManageService->checkIfPhoneIsTest([$form->phone]);

            $leadId = $this->leadRepository->save($lead);

            $this->createFlightSegments($leadId, $form->segments);

        });
    }

    /**
     * @param int $leadId
     * @param SegmentForm[] $segments
     */
    private function createFlightSegments(int $leadId, array $segments): void
    {
        foreach ($segments as $item) {
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
     * @param SegmentForm[] $segmentsForm
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
}
