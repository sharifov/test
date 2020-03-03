<?php
namespace modules\hotel\src\helpers;

use modules\hotel\models\HotelQuote;

class HotelQuoteHelper
{
	/**
	 * @param HotelQuote $hotelQuote
	 * @return HotelQuoteRoomPriceDataDTO
	 */
	public static function getPricesData(HotelQuote $hotelQuote): HotelQuoteRoomPriceDataDTO
	{
		/** @var $prices HotelQuoteRoomPriceDTO[] */
		$prices = [];

		$dtoTotal = new HotelQuoteRoomTotalPriceDTO();
		foreach ($hotelQuote->hotelQuoteRooms as $room) {
			$dtoRoom = new HotelQuoteRoomPriceDTO(
				$room->hqr_amount,
				$room->hqr_cancel_amount,
				$room->hqr_service_fee_percent,
				$room->hqr_system_mark_up,
				$room->hqr_agent_mark_up
			);

			$prices[] = $dtoRoom;

			$dtoTotal->net += $dtoRoom->net;
			$dtoTotal->systemMarkup += $dtoRoom->systemMarkup;
			$dtoTotal->agentMarkup += $dtoRoom->agentMarkup;
			$dtoTotal->sellingPrice += $dtoRoom->sellingPrice;
			$dtoTotal->serviceFeeSum += $dtoRoom->serviceFeeSum;
		}

		return new HotelQuoteRoomPriceDataDTO(
			$dtoTotal,
			$prices
		);
	}
}