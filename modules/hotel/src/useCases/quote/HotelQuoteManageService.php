<?php

namespace modules\hotel\src\useCases\quote;

use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\src\entities\hotelQuoteRoom\HotelQuoteRoomRepository;
use modules\hotel\src\helpers\HotelQuoteHelper;
use modules\hotel\src\services\hotelQuote\HotelQuotePriceCalculator;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\services\OfferPriceUpdater;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\product\ProductQuoteHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\CurrencyHelper;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteManageService
 * @package modules\hotel\src\useCases\quote
 *
 * @property TransactionManager $transactionManager
 * @property HotelQuoteRoomRepository $hotelQuoteRoomRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property OfferPriceUpdater $offerPriceUpdater
 */
class HotelQuoteManageService
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var HotelQuoteRoomRepository
     */
    private $hotelQuoteRoomRepository;
    /**
     * @var ProductQuoteRepository
     */
    private $productQuoteRepository;

    private OrderPriceUpdater $orderPriceUpdater;
    private OfferPriceUpdater $offerPriceUpdater;

    public function __construct(
        TransactionManager $transactionManager,
        HotelQuoteRoomRepository $hotelQuoteRoomRepository,
        ProductQuoteRepository $productQuoteRepository,
        OrderPriceUpdater $orderPriceUpdater,
        OfferPriceUpdater $offerPriceUpdater
    ) {
        $this->transactionManager = $transactionManager;
        $this->hotelQuoteRoomRepository = $hotelQuoteRoomRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->offerPriceUpdater = $offerPriceUpdater;
    }

    /**
     * @param HotelQuoteRoom $hotelQuoteRoom
     * @param float $markup
     * @throws \Throwable
     */
    public function updateAgentMarkup(HotelQuoteRoom $hotelQuoteRoom, float $markup): void
    {
        $this->transactionManager->wrap(function () use ($hotelQuoteRoom, $markup) {
            $hotelQuoteRoom->hqr_agent_mark_up = $markup;
            $this->hotelQuoteRoomRepository->save($hotelQuoteRoom);

            //update product quote prices
            $productQuote = $hotelQuoteRoom->hqrHotelQuote->hqProductQuote;
            $prices = (new HotelQuotePriceCalculator())->calculate($hotelQuoteRoom->hqrHotelQuote, $productQuote->pq_origin_currency_rate);
            $productQuote->updatePrices(
                $prices['originPrice'],
                $prices['appMarkup'],
                $prices['agentMarkup'],
            );
            $this->productQuoteRepository->save($productQuote);

            if ($productQuote->pq_order_id) {
                $this->orderPriceUpdater->update($productQuote->pq_order_id);
            }

            $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $productQuote->pq_id])->column();
            foreach ($offers as $offer) {
                $this->offerPriceUpdater->update($offer);
            }
        });
    }
}
