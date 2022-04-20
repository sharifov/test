<?php

namespace src\services\lead;

use common\models\Currency;
use common\models\LeadPreferences;
use src\repositories\lead\LeadPreferencesRepository;

/**
 * Class LeadAssignService
 * @property LeadPreferencesRepository $leadPreferencesRepository
 */
class LeadPreferencesCloneService
{
    private LeadPreferencesRepository $leadPreferencesRepository;

    public function __construct(
        LeadPreferencesRepository $leadPreferencesRepository
    ) {
        $this->leadPreferencesRepository = $leadPreferencesRepository;
    }

    public function cloneLeadPreferenceCurrency(int $leadId, int $cloneLeadId): LeadPreferences
    {
        $leadPreferences = LeadPreferences::find()->where(['lead_id' => $leadId])->one();
        $currency = $leadPreferences->pref_currency ?? Currency::getDefaultCurrencyCode();

        $cloneLeadPreferences = LeadPreferences::createOnlyCurrency($cloneLeadId, $currency);
        $this->leadPreferencesRepository->save($cloneLeadPreferences);
        return $cloneLeadPreferences;
    }
}
