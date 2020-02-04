<?php
namespace modules\flight\src\entities\flightQuotePaxPrice;

use modules\flight\models\FlightQuotePaxPrice;

class FlightQuotePaxPriceQuery
{
	/**
	 * @param int $fqId
	 * @param int $paxCode
	 * @return FlightQuotePaxPrice|null
	 */
	public static function findByFlightIdAndPaxCodeId(int $fqId, int $paxCode): ?FlightQuotePaxPrice
	{
		return FlightQuotePaxPrice::find()->where(['qpp_flight_quote_id' => $fqId, 'qpp_flight_pax_code_id' => $paxCode])->one();
	}
}