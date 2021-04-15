<?php

namespace modules\lead\src\services;

use common\models\Department;
use common\models\Lead;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\model\leadOrder\services\LeadOrderService;
use sales\repositories\lead\LeadRepository;

/**
 * Class LeadFailBooking
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property LeadRepository $leadRepository
 * @property LeadOrderService $leadOrderService
 */
class LeadFailBooking
{
    private ProductQuoteRepository $productQuoteRepository;
    private LeadRepository $leadRepository;
    private LeadOrderService $leadOrderService;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        LeadRepository $leadRepository,
        LeadOrderService $leadOrderService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->leadRepository = $leadRepository;
        $this->leadOrderService = $leadOrderService;
    }

    public function create(int $quoteId)
    {
        $quote = $this->productQuoteRepository->find($quoteId);

        $order = $this->getOrder($quote);

        $lead = Lead::createBookFailed(
            $order->or_project_id,
            Department::DEPARTMENT_SALES
        );
        $this->leadRepository->save($lead);

        $this->leadOrderService->create($lead->id, $order->or_id);

        $quote->pqProduct->pr_lead_id = $lead->id;
        $quote->pqProduct->save();

        $this->connectClient();
    }

    private function getOrder(ProductQuote $quote): Order
    {
        if (!$quote->pq_order_id) {
            throw new \DomainException('Order not found. QuoteId: ' . $quote->pq_id);
        }

        return $quote->pqOrder;
    }

    private function connectClient()
    {
    }
}
