<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use common\models\Lead;
use modules\webEngage\settings\WebEngageSettings;

/**
 * Class AbstractWebEngageLeadEventData
 *
 * @property Lead $lead
 * @property LeadEventDataService $leadService
 * @project WebEngageSettings $settings
 */
abstract class AbstractLeadEventData
{
    private Lead $lead;
    private LeadEventDataService $leadService;
    private WebEngageSettings $settings;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->leadService = new LeadEventDataService($lead);
        $this->settings = new WebEngageSettings();
    }

    public function getEventData(): array
    {
        return [
            'phone' => $this->lead->l_client_phone,
            'name' => $this->lead->client->getFullName(),
            'email' => $this->lead->l_client_email,
            'lead_id' => $this->lead->id,
            'origin' => $this->leadService->getOrigin(),
            'destination' =>  $this->leadService->getDestination(),
            'departure-date' => $this->leadService->getDepartureDate(),
            'return-date' => $this->leadService->getReturnDate(),
            'cabin' => $this->lead->getCabinClassName(),
            'route' => $this->leadService->getRoute(),
            'flight-type' => $this->lead->getFlightTypeName(),
            'adult' => $this->lead->adults,
            'child' => $this->lead->children,
            'infant' => $this->lead->infants,
            'price' => null,
            'isTest' => $this->settings->isTest(),
            'sourceCID' => $this->lead->source->cid ?? null,
            'originCity' => $this->leadService->getOriginCity(),
            'destinationCity' => $this->leadService->getDestinationCity(),
        ];
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getLeadService(): LeadEventDataService
    {
        return $this->leadService;
    }
}
