<?php
namespace modules\hotel\src\useCases\quote;

use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\src\entities\hotelQuoteRoom\HotelQuoteRoomRepository;
use modules\hotel\src\helpers\HotelQuoteHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\product\ProductQuoteHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteManageService
 * @package modules\hotel\src\useCases\quote
 *
 * @property TransactionManager $transactionManager
 * @property HotelQuoteRoomRepository $hotelQuoteRoomRepository
 * @property ProductQuoteRepository $productQuoteRepository
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

	public function __construct(TransactionManager $transactionManager, HotelQuoteRoomRepository $hotelQuoteRoomRepository, ProductQuoteRepository $productQuoteRepository)
	{
		$this->transactionManager = $transactionManager;
		$this->hotelQuoteRoomRepository = $hotelQuoteRoomRepository;
		$this->productQuoteRepository = $productQuoteRepository;
	}

	/**
	 * @param HotelQuoteRoom $hotelQuoteRoom
	 * @param float $markup
	 * @throws \Throwable
	 */
	public function updateAgentMarkup(HotelQuoteRoom $hotelQuoteRoom, float $markup): void
	{
		$this->transactionManager->wrap( function () use ($hotelQuoteRoom, $markup) {
			$hotelQuoteRoom->hqr_agent_mark_up = $markup;
			$this->hotelQuoteRoomRepository->save($hotelQuoteRoom);

			$productQuote = $hotelQuoteRoom->hqrHotelQuote->hqProductQuote;
			$this->calcProductQuotePrice($productQuote, $hotelQuoteRoom->hqrHotelQuote);
		});
	}

	/**
	 * @param ProductQuote $productQuote
	 * @param HotelQuote $hotelQuote
	 */
	private function calcProductQuotePrice(ProductQuote $productQuote, HotelQuote $hotelQuote): void
	{
		$priceData = HotelQuoteHelper::getPricesData($hotelQuote);

		$systemPrice = ProductQuoteHelper::calcSystemPrice($priceData->total->sellingPrice, $productQuote->pq_origin_currency);
		$productQuote->setQuotePrice(
			ProductQuoteHelper::roundPrice($priceData->total->net),
			$systemPrice,
			ProductQuoteHelper::roundPrice($systemPrice * $productQuote->pq_client_currency_rate),
			ProductQuoteHelper::roundPrice($priceData->total->serviceFeeSum)
		);
		$productQuote->recalculateProfitAmount();
		$this->productQuoteRepository->save($productQuote);
	}

}