<?php

namespace src\services\lead;

use common\models\LeadPreferences;
use src\repositories\lead\LeadPreferencesRepository;
use src\services\ServiceFinder;
use src\services\TransactionManager;
use yii\web\NotFoundHttpException;


/**
 * Class LeadAssignService
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property TransactionManager $transactionManager
 * @property EventDispatcher $eventDispatcher
 * @property ServiceFinder $serviceFinder
 */

class LeadPreferencesCloneService
{

    private $leadPreferencesRepository;
    private $transactionManager;
    private $eventDispatcher;
    private $serviceFinder;

    public function __construct(
        LeadPreferencesRepository $leadPreferencesRepository,
        TransactionManager $transactionManager,
        ServiceFinder $serviceFinder
    ) {
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->transactionManager = $transactionManager;
        $this->serviceFinder = $serviceFinder;
    }

    /**
     * @param int $leadId
     * @param int $cloneLeadId
     * @return LeadPreferences
     * @throws \Throwable
     */

    public function cloneLeadPreferences($leadId, $cloneLeadId): LeadPreferences
    {

        $lead = $this->serviceFinder->leadFind($cloneLeadId);
        $leadPreferences = LeadPreferences::find()->where(['lead_id' => $leadId])->one();

        $clone = $this->transactionManager->wrap(function () use ($lead, $leadPreferences) {

            $currency = $leadPreferences->pref_currency;

            $clone = $leadPreferences->createClone($lead->id, $currency);
            $this->leadPreferencesRepository->save($clone);

            return $clone;
        });

        return $clone;
    }
    

}