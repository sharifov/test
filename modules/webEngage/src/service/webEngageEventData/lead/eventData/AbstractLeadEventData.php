<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use common\models\Lead;

/**
 * Class AbstractWebEngageLeadEventData
 *
 * @property Lead $lead
 * @property LeadEventDataService $leadService
 */
abstract class AbstractLeadEventData
{
    private Lead $lead;
    private LeadEventDataService $leadService;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->leadService = new LeadEventDataService($lead);
    }

    public function getEventData(): array
    {
        $result['phone'] = $this->lead->l_client_phone;
        $result['name'] = $this->lead->client->getFullName();
        $result['email'] = $this->lead->l_client_email;
        $result['lead_from_lp'] = $this->lead->request_ip;
        $result['lead_id'] = $this->lead->id;
        $result['origin'] = $this->leadService->origin;
        $result['destination'] =  $this->leadService->destination;
        $result['departure-date'] = $this->leadService->departureDate;
        $result['return-date'] = $this->leadService->returnDate; /* TODO::  */
        $result['cabin'] = $this->lead->cabin;
        $result['route'] = $this->leadService->route;
        $result['flight-type'] = $this->lead->trip_type;
        $result['adult'] = $this->lead->adults;
        $result['child'] = $this->lead->children;
        $result['infant'] = $this->lead->infants;
        $result['price'] = null;  /* TODO::  */

        return $result;
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
