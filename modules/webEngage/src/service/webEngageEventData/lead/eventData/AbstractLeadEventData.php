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
        $result['phone'] = $this->lead->l_client_phone;
        $result['name'] = $this->lead->client->getFullName();
        $result['email'] = $this->lead->l_client_email;
        $result['lead_from_lp'] = $this->lead->request_ip;
        $result['lead_id'] = $this->lead->id;
        $result['origin'] = $this->leadService->getOrigin();
        $result['destination'] =  $this->leadService->getDestination();
        $result['departure-date'] = $this->leadService->getDepartureDate();
        $result['return-date'] = $this->leadService->getReturnDate();
        $result['cabin'] = $this->lead->cabin;
        $result['route'] = $this->leadService->getRoute();
        $result['flight-type'] = $this->lead->trip_type;
        $result['adult'] = $this->lead->adults;
        $result['child'] = $this->lead->children;
        $result['infant'] = $this->lead->infants;
        $result['price'] = null;
        $result['isTest'] = $this->settings->isTest();

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
