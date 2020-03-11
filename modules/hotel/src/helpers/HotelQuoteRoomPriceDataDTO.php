<?php
namespace modules\hotel\src\helpers;

/**
 * Class HotelQuoteRoomPriceDataDTO
 * @package modules\hotel\src\helpers
 *
 * @property HotelQuoteRoomTotalPriceDTO $total
 * @property HotelQuoteRoomPriceDTO[] $prices
 */
class HotelQuoteRoomPriceDataDTO
{
	/**
	 * @var HotelQuoteRoomTotalPriceDTO
	 */
	public $total;

	/**
	 * @var HotelQuoteRoomPriceDTO[]
	 */
	public $prices;

	public function __construct(HotelQuoteRoomTotalPriceDTO $total, array $prices)
	{
		$this->total = $total;
		$this->prices = $prices;
	}
}