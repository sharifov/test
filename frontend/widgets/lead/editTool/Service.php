<?php

namespace frontend\widgets\lead\editTool;

use common\models\Lead;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;

/**
 * Class Service
 *
 * @property LeadRepository $leadRepository
 * @property ServiceFinder $finder
 */
class Service
{
    private $leadRepository;
    private $finder;

    public function __construct(LeadRepository $leadRepository, ServiceFinder $finder)
    {
        $this->leadRepository = $leadRepository;
        $this->finder = $finder;
    }

    /**
     * @param int|Lead $lead
     * @param Form $form
     */
    public function edit($lead, Form $form): void
    {
        $lead = $this->finder->leadFind($lead);
        $lead->detachBehavior('timestamp');
        $lead->attributes = $form->attributes;
        $this->leadRepository->save($lead);
    }
}
