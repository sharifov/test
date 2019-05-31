<?php

namespace sales\services\lead;

use sales\forms\lead\ItineraryForm;
use sales\repositories\lead\LeadRepository;

class LeadManageService
{
    private $leads;

    public function __construct(LeadRepository $leads)
    {
        $this->leads = $leads;
    }

    public function editItinerary($id, ItineraryForm $form): void
    {
        $lead = $this->leads->get($id);
        $lead->editItinerary(
            $form->cabin,
            $form->adults,
            $form->children,
            $form->infants
        );
        $this->leads->save($lead);
    }
}