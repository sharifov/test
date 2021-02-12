<?php

namespace modules\cruise\src\useCase\updateMarkup;

use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use sales\helpers\product\ProductQuoteHelper;
use sales\services\TransactionManager;
use sales\repositories\product\ProductQuoteRepository;

class CruiseMarkupService
{
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(TransactionManager $transactionManager, ProductQuoteRepository $productQuoteRepository)
    {
        $this->transactionManager = $transactionManager;
        $this->productQuoteRepository = $productQuoteRepository;
    }

    public function updateAgentMarkup(int $quoteId, $value)
    {
        $this->transactionManager->wrap(function () use ($quoteId, $value) {
            $cruiseQuote = CruiseQuote::findOne($quoteId);
            if (!$cruiseQuote) {
                throw new \DomainException('Not found Cruise Quote');
            }
            $cruiseQuote->crq_agent_mark_up = $value;
            if (!$cruiseQuote->save()) {
                throw new \DomainException('Update error');
            }
            $productQuote = $cruiseQuote->productQuote;

            $serviceFeeSum = (($cruiseQuote->crq_amount + $cruiseQuote->crq_system_mark_up + $cruiseQuote->crq_agent_mark_up) * $cruiseQuote->crq_service_fee_percent / 100);
            $totalSystemPrice = $cruiseQuote->crq_amount + $serviceFeeSum + $cruiseQuote->crq_system_mark_up + $cruiseQuote->crq_agent_mark_up;
            $systemPrice = ProductQuoteHelper::calcSystemPrice((float)$totalSystemPrice, $productQuote->pq_origin_currency);
            $productQuote->setQuotePrice(
                (float)$cruiseQuote->crq_amount,
                (float)$systemPrice,
                ProductQuoteHelper::roundPrice($systemPrice * $productQuote->pq_client_currency_rate),
                $serviceFeeSum
            );
            $productQuote->recalculateProfitAmount();
            $this->productQuoteRepository->save($productQuote);
        });
    }
}
