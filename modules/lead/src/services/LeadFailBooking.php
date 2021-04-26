<?php

namespace modules\lead\src\services;

use common\models\Department;
use common\models\Lead;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderContact\OrderContact;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteLead\service\ProductQuoteLeadService;
use modules\product\src\entities\productQuoteOrigin\service\ProductQuoteOriginService;
use modules\product\src\services\ProductCloneService;
use sales\model\leadOrder\services\LeadOrderService;
use sales\model\leadProduct\services\LeadProductService;
use sales\repositories\lead\LeadRepository;
use sales\services\TransactionManager;

/**
 * Class LeadFailBooking
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property LeadRepository $leadRepository
 * @property LeadOrderService $leadOrderService
 * @property LeadProductService $leadProductService
 * @property ProductCloneService $productCloneService
 * @property ProductQuoteLeadService $productQuoteLeadService
 * @property ProductQuoteOriginService $productQuoteOriginService
 * @property TransactionManager $transactionManager
 */
class LeadFailBooking
{
    private ProductQuoteRepository $productQuoteRepository;
    private LeadRepository $leadRepository;
    private LeadOrderService $leadOrderService;
    private LeadProductService $leadProductService;
    private ProductCloneService $productCloneService;
    private ProductQuoteLeadService $productQuoteLeadService;
    private ProductQuoteOriginService $productQuoteOriginService;
    private TransactionManager $transactionManager;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        LeadRepository $leadRepository,
        LeadOrderService $leadOrderService,
        LeadProductService $leadProductService,
        ProductCloneService $productCloneService,
        ProductQuoteLeadService $productQuoteLeadService,
        ProductQuoteOriginService $productQuoteOriginService,
        TransactionManager $transactionManager
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->leadRepository = $leadRepository;
        $this->leadOrderService = $leadOrderService;
        $this->leadProductService = $leadProductService;
        $this->productCloneService = $productCloneService;
        $this->productQuoteLeadService = $productQuoteLeadService;
        $this->productQuoteOriginService = $productQuoteOriginService;
        $this->transactionManager = $transactionManager;
    }

    public function create(int $quoteId, ?int $createdUserId)
    {
        $quote = $this->productQuoteRepository->find($quoteId);

        $order = $this->getOrder($quote);

        $this->transactionManager->wrap(function () use ($order, $quote, $createdUserId) {
            $lead = Lead::createBookFailed(
                $order->or_project_id,
                Department::DEPARTMENT_SALES,
                $this->getClientId($order->or_id)
            );
            $this->leadRepository->save($lead);

            $this->leadOrderService->create($lead->id, $order->or_id);
            $this->leadProductService->create($lead->id, $quote->pq_product_id);
            $this->productQuoteLeadService->create($quote->pq_id, $lead->id);
            $product = $this->productCloneService->clone($quote->pq_product_id, $lead->id, $createdUserId);
            $this->productQuoteOriginService->create($product->pr_id, $quote->pq_id);
        });
    }

    private function getOrder(ProductQuote $quote): Order
    {
        if (!$quote->pq_order_id) {
            throw new \DomainException('Order not found. QuoteId: ' . $quote->pq_id);
        }

        return $quote->pqOrder;
    }

    private function getClientId(int $orderId): int
    {
        $clientId = OrderContact::find()->select(['oc_client_id'])->byOrderId($orderId)->asArray()->scalar();

        if ($clientId) {
            return (int)$clientId;
        }

        throw new \DomainException('Not found clientId in OrderContact. OrderId: ' . $orderId);
    }
}
