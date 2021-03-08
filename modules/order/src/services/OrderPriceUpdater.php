<?php

namespace modules\order\src\services;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\services\CurrencyHelper;

/**
 * Class OrderPriceUpdater
 *
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property OrderRepository $orderRepository
 */
class OrderPriceUpdater
{
    private OrderUserProfitRepository $orderUserProfitRepository;
    private OrderRepository $orderRepository;

    public function __construct(OrderUserProfitRepository $orderUserProfitRepository, OrderRepository $orderRepository)
    {
        $this->orderUserProfitRepository = $orderUserProfitRepository;
        $this->orderRepository = $orderRepository;
    }

    public function update(int $orderId): void
    {
        $order = $this->orderRepository->find($orderId);

        $quotes = ProductQuote::find()->andWhere(['pq_order_id' => $order->or_id])->all();

        $appTotal = 0;
        $appMarkUp = 0;
        $agentMarkup = 0;
        $profitAmount = 0;
        foreach ($quotes as $quote) {
            if ($quote->pq_price) {
                $appTotal += $quote->pq_price;
            }
            foreach ($quote->productQuoteOptions as $option) {
                if ($option->pqo_price) {
                    $appTotal += $option->pqo_price;
                }
                if ($option->pqo_extra_markup) {
                    $appTotal += $option->pqo_extra_markup;
                    $agentMarkup += $option->pqo_extra_markup;
                    $profitAmount += $option->pqo_extra_markup;
                }
            }
            if ($quote->pq_app_markup) {
                $appMarkUp += $quote->pq_app_markup;
            }
            if ($quote->pq_agent_markup) {
                $agentMarkup += $quote->pq_agent_markup;
            }
            if ($quote->pq_profit_amount) {
                $profitAmount += $quote->pq_profit_amount;
            }
        }
        $order->or_app_total = $appTotal;
        $order->or_app_markup = $appMarkUp;
        $order->or_agent_markup = $agentMarkup;
        $order->or_profit_amount = $profitAmount;
        if ($order->or_client_currency_rate) {
            $order->or_client_total = CurrencyHelper::convertFromBaseCurrency($order->or_app_total, $order->or_client_currency_rate);
        } else {
            $order->or_client_total = $order->or_app_total;
        }
        $this->orderRepository->save($order);

        foreach ($order->orderUserProfit as $userProfit) {
            $userProfit->updateAmount($order->or_profit_amount ?? 0.00);
            $this->orderUserProfitRepository->save($userProfit);
        }
    }
}
