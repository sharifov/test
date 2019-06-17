<?php

namespace sales\services\lead;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\LeadCreateForm;
use sales\forms\lead\SegmentEditForm;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\TransactionManager;
use sales\repositories\NotFoundException;

/**
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $segmentRepository
 * @property ClientEmailRepository $clientEmailRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property ClientRepository $clientRepository
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
    private $transaction;

    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $segmentRepository,
        ClientEmailRepository $clientEmailRepository,
        ClientPhoneRepository $clientPhoneRepository,
        LeadPreferencesRepository $leadPreferencesRepository,
        ClientRepository $clientRepository,
        TransactionManager $transaction)
    {
        $this->leadRepository = $leadRepository;
        $this->segmentRepository = $segmentRepository;
        $this->clientEmailRepository = $clientEmailRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->clientRepository = $clientRepository;
        $this->transaction = $transaction;
    }

    public function create(LeadCreateForm $form, $employeeId): Lead
    {
        $client = null;
        foreach ($form->phones as $phone) {
            try {
                if (($clientPhone = $this->clientPhoneRepository->getByPhone($phone->phone)) && ($client = $clientPhone->client)) {
                    break;
                }
            } catch (NotFoundException $e) {}
        }

        $lead = $this->transaction->wrap(function () use ($form, $employeeId, $client) {

            if ($client) {
                $clientId = $client->id;
            } else {
                $client = Client::create(
                    $form->client->firstName,
                    $form->client->middleName,
                    $form->client->lastName
                );
                $clientId = $this->clientRepository->save($client);
            }

            $lead = Lead::create(
                $clientId,
                $form->client->firstName,
                $form->client->lastName,
                $employeeId,
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

            $lead->setTripType($this->calculateTripType($form->segments));

            $leadId = $this->leadRepository->save($lead);

            foreach ($form->phones as $phoneForm) {
                try {
                    $clientPhone = $this->clientPhoneRepository->getByPhone($phoneForm->phone);
                } catch (NotFoundException $e) {
                    $clientPhone = null;
                }
                if (($clientPhone && (($clientPhone->client && $clientPhone->client->id !== $clientId) || !$clientPhone->client)) || !$clientPhone) {
                    $phone = ClientPhone::create(
                        $phoneForm->phone,
                        $clientId
                    );
                    $this->clientPhoneRepository->save($phone);
                }
            }

            foreach ($form->emails as $emailForm) {
                try {
                    $clientEmail = $this->clientEmailRepository->getByEmail($emailForm->email);
                } catch (NotFoundException $e) {
                    $clientEmail = null;
                }
                if (($clientEmail && (($clientEmail->client && $clientEmail->client->id !== $clientId) || !$clientEmail->client)) || !$clientEmail) {
                    $email = ClientEmail::create(
                        $emailForm->email,
                        $clientId
                    );
                    $this->clientEmailRepository->save($email);
                }
            }

            foreach ($form->segments as $segmentForm) {
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

            $preferences = LeadPreferences::create(
                $leadId,
                $form->preferences->marketPrice,
                $form->preferences->clientsBudget,
                $form->preferences->numberStops
            );
            $this->leadPreferencesRepository->save($preferences);

            return $lead;

        });

        return $lead;
    }

    /**
     * @param $id
     * @param ItineraryEditForm $form
     * @throws \Exception
     */
    public function editItinerary($id, ItineraryEditForm $form): void
    {
        $lead = $this->leadRepository->get($id);

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
                $segment = $this->getSegment($segmentForm, $lead->id);
                $newSegmentsIds[] = $this->segmentRepository->save($segment);
            }
            $this->segmentRepository->removeOld($lead->leadFlightSegments, $newSegmentsIds);

            $this->leadRepository->save($lead);

        });
    }

    private static function calculateTripType(array $segments): string
    {
        $countSegments = count($segments);
        if ($countSegments === 0) {
            throw new \InvalidArgumentException('Segments must be more than 0');
        }
        if ($countSegments === 1) {
            return Lead::TRIP_TYPE_ONE_WAY;
        }
        if ($countSegments === 2) {
            $origin1 = strtoupper($segments[0]->origin);
            $destination1 = strtoupper($segments[0]->destination);
            $origin2 = strtoupper($segments[1]->origin);
            $destination2 = strtoupper($segments[1]->destination);
            if ($origin1 === $destination2 && $destination1 === $origin2) {
                return Lead::TRIP_TYPE_ROUND_TRIP;
            }
        }
        return Lead::TRIP_TYPE_MULTI_DESTINATION;
    }

    private function getSegment(SegmentEditForm $segmentForm, $leadId): LeadFlightSegment
    {
        if ($segmentForm->segmentId) {
            $segment = $this->segmentRepository->get($segmentForm->segmentId);
            $segment->edit(
                $segmentForm->origin,
                $segmentForm->destination,
                $segmentForm->departure,
                $segmentForm->flexibility,
                $segmentForm->flexibilityType
            );
        } else {
            $segment = LeadFlightSegment::create(
                $leadId,
                $segmentForm->origin,
                $segmentForm->destination,
                $segmentForm->departure,
                $segmentForm->flexibility,
                $segmentForm->flexibilityType
            );
        }
        return $segment;
    }

}
