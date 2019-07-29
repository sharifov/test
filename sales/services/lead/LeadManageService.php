<?php

namespace sales\services\lead;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\LeadCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\forms\lead\PreferencesCreateForm;
use sales\forms\lead\SegmentCreateForm;
use sales\forms\lead\SegmentEditForm;
use sales\repositories\airport\AirportRepository;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\repositories\NotFoundException;
use sales\services\TransactionManager;
use Yii;

/**
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $segmentRepository
 * @property ClientEmailRepository $clientEmailRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property ClientRepository $clientRepository
 * @property LeadHashGenerator $leadHashGenerator
 * @property AirportRepository $airportRepository
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
        $this->transaction = $transaction;
    }

    /**
     * @param LeadCreateForm $form
     * @param int $employeeId
     * @return Lead
     * @throws \Exception
     */
    public function create(LeadCreateForm $form, int $employeeId): Lead
    {

        $lead = $this->transaction->wrap(function () use ($form, $employeeId) {

            $clientId = $this->getClientId($form->phones, $form->client);

            $lead = Lead::create(
                $clientId,
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
                $form->clientEmail
            );

            $lead->take($employeeId);

            $phones = $this->createClientPhones($clientId, $form->phones);

            $this->createClientEmails($clientId, $form->emails);

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
                $lead->setDuplicate($origin->id);
            }

            $lead->setTripType(self::calculateTripType($form->segments));

            $leadId = $this->leadRepository->save($lead);

            $this->createFlightSegments($leadId, $form->segments);

            $this->createLeadPreferences($leadId, $form->preferences);

            return $lead;

        });

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
     * @param int $clientId
     * @param EmailCreateForm[] $emailsForm
     */
    private function createClientEmails(int $clientId, array $emailsForm): void
    {
        foreach ($emailsForm as $emailForm) {
            if ($emailForm->email && !$this->clientEmailRepository->exists($clientId, $emailForm->email)) {
                $email = ClientEmail::create(
                    $emailForm->email,
                    $clientId
                );
                $this->clientEmailRepository->save($email);
            }
        }
    }

    /**
     * @param int $clientId
     * @param PhoneCreateForm[] $phonesForm
     * @return array
     */
    private function createClientPhones(int $clientId, array $phonesForm): array
    {
        $phones = [];
        foreach ($phonesForm as $phoneForm) {
            if ($phoneForm->phone && !$this->clientPhoneRepository->exists($clientId, $phoneForm->phone)) {
                $phone = ClientPhone::create(
                    $phoneForm->phone,
                    $clientId
                );
                $this->clientPhoneRepository->save($phone);
            }
            $phones[] = $phoneForm->phone;
        }
        return $phones;
    }

    /**
     * @param PhoneCreateForm[] $phonesForm
     * @param ClientCreateForm $clientForm
     * @return int
     */
    private function getClientId(array $phonesForm, ClientCreateForm $clientForm): int
    {
        foreach ($phonesForm as $phoneForm) {
            if (($clientPhone = $this->clientPhoneRepository->getByPhone($phoneForm->phone)) && ($client = $clientPhone->client)) {
                return $client->id;
            }
        }

        $client = Client::create(
            $clientForm->firstName,
            $clientForm->middleName,
            $clientForm->lastName
        );
        return $this->clientRepository->save($client);
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
        $countSegments = count($segments);
        if ($countSegments === 0) {
            return '';
        }
        if ($countSegments === 1) {
            return Lead::TRIP_TYPE_ONE_WAY;
        }
        if ($countSegments === 2) {

            $airport1 = $segments[0]->origin;
            $airport2 = $segments[0]->destination;
            $airport3 = $segments[1]->origin;
            $airport4 = $segments[1]->destination;
            if ($airport1 == $airport4 && $airport3 == $airport2) {
                return Lead::TRIP_TYPE_ROUND_TRIP;
            }
            if (
                ($airport1 = $this->airportRepository->getByIata($segments[0]->origin)) &&
                ($airport2 = $this->airportRepository->getByIata($segments[0]->destination)) &&
                ($airport3 = $this->airportRepository->getByIata($segments[1]->origin)) &&
                ($airport4 = $this->airportRepository->getByIata($segments[1]->destination))
            ) {
                if ($airport1->city == $airport4->city && $airport3->city == $airport2->city) {
                    return Lead::TRIP_TYPE_ROUND_TRIP;
                }
            }

        }
        return Lead::TRIP_TYPE_MULTI_DESTINATION;
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
