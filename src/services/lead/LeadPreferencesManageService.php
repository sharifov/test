<?php

namespace src\services\lead;

use common\models\Currency;
use common\models\Lead;
use common\models\LeadPreferences;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataRepository;
use src\auth\Auth;
use src\forms\lead\LeadPreferencesForm;
use src\model\leadOrder\entity\LeadOrder;
use src\repositories\lead\LeadPreferencesRepository;
use src\repositories\lead\LeadRepository;
use src\services\ServiceFinder;
use src\services\TransactionManager;
use Yii;

/**
 * Class LeadPreferencesManageService
 * @package src\services\lead
 *
 * @property ServiceFinder $finder
 * @property LeadRepository $leadRepository
 * @property TransactionManager $transactionManager
 * @property LeadPreferencesRepository $leadPreferencesRepository
 * @property OrderDataRepository $orderDataRepository
 */
class LeadPreferencesManageService
{
    private $finder;
    private $leadRepository;
    private $transactionManager;
    private $leadPreferencesRepository;
    private OrderDataRepository $orderDataRepository;

    /**
     * LeadPreferencesManageService constructor.
     * @param ServiceFinder $finder
     * @param TransactionManager $transactionManager
     * @param LeadRepository $leadRepository
     * @param LeadPreferencesRepository $leadPreferencesRepository
     */
    public function __construct(
        ServiceFinder $finder,
        TransactionManager $transactionManager,
        LeadRepository $leadRepository,
        LeadPreferencesRepository $leadPreferencesRepository,
        OrderDataRepository $orderDataRepository
    ) {
        $this->finder = $finder;
        $this->leadRepository = $leadRepository;
        $this->transactionManager = $transactionManager;
        $this->leadPreferencesRepository = $leadPreferencesRepository;
        $this->orderDataRepository = $orderDataRepository;
    }

    /**
     * @param LeadPreferencesForm $form
     * @param int|Lead $lead
     * @param int $userId
     * @throws \Throwable
     */
    public function edit(LeadPreferencesForm $form, Lead $lead, int $userId): void
    {
        $lead = $this->finder->leadFind($lead);
        $leadPreferences = $lead->leadPreferences;

        $this->transactionManager->wrap(function () use ($form, $lead, $leadPreferences, $userId) {
            $dto = new LeadAbacDto($lead, $userId);

            /** @abac new LeadAbacDto($lead), LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_SET_DELAY_CHARGE, Lead preferences update Delay Charge access */
            $delayChargeAccess = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_SET_DELAY_CHARGE);
            $lead->editDelayedChargeAndNote($form->delayedCharge, $form->notesForExperts, $delayChargeAccess);
            if ($lead->l_client_lang !== $form->clientLang) {
                $lead->l_client_lang = $form->clientLang;
//                $this->processOrderData($lead->id, $lead->l_client_lang);
            }
            $this->leadRepository->save($lead);

            if ($leadPreferences) {
                $leadPreferences->edit(
                    $form->marketPrice,
                    $form->clientsBudget,
                    $form->numberStops,
                    $form->currency
                );
            } else {
                $leadPreferences = LeadPreferences::create($lead->id, $form->marketPrice, $form->clientsBudget, $form->numberStops, $form->currency);
            }

            if (!$form->canManageCurrency) {
                $leadPreferences->pref_currency = $leadPreferences->oldAttributes['pref_currency'] ?? Currency::getDefaultCurrencyCodeByDb();
            }

            $this->leadPreferencesRepository->save($leadPreferences);
        });
    }

    private function processOrderData(int $leadId, ?string $lang): void
    {
        $orders = LeadOrder::find()->select(['lo_order_id'])->byLead($leadId)->asArray()->column();
        if (!$orders) {
            return;
        }
        $orderData = OrderData::find()->andWhere(['od_order_id' => $orders])->all();
        if (!$orderData) {
            return;
        }
        foreach ($orderData as $data) {
            $data->changeLanguage($lang);
            $this->orderDataRepository->save($data);
        }
    }
}
