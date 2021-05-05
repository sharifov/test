<?php

namespace sales\services\lead;

use common\models\Lead;
use common\models\LeadPreferences;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataRepository;
use sales\forms\lead\LeadPreferencesForm;
use sales\model\leadOrder\entity\LeadOrder;
use sales\repositories\lead\LeadPreferencesRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;

/**
 * Class LeadPreferencesManageService
 * @package sales\services\lead
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
     * @throws \Throwable
     */
    public function edit(LeadPreferencesForm $form, Lead $lead): void
    {
        $lead = $this->finder->leadFind($lead);
        $leadPreferences = $lead->leadPreferences;

        $this->transactionManager->wrap(function () use ($form, $lead, $leadPreferences) {
            $lead->editDelayedChargeAndNote($form->delayedCharge, $form->notesForExperts);
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
