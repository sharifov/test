<?php

namespace modules\order\src\listeners\lead;

use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\Order;
use sales\repositories\lead\LeadRepository;

/**
 * Class LeadSoldListener
 *
 * @property LeadRepository $leadRepository
 */
class LeadSoldListener
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function handle(OrderCompletedEvent $event): void
    {
        try {
            $order = Order::findOne($event->orderId);

            if (!$order) {
                return;
            }

            if (!$order->or_lead_id) {
                return;
            }

            $lead = $order->orLead;

            $lead->sold($lead->employee_id);
            $this->leadRepository->save($lead);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Transfer Lead to Sold error',
                'error' => $e->getMessage(),
                'orderId' => $event->orderId,
            ], 'LeadSoldListener');
        }
    }
}
