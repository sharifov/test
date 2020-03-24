<?php
namespace modules\flight\src\useCases\flightQuote\createManually\helpers;

use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;

class FlightQuotePaxPriceHelper
{
	public static function getQuotePaxPriceFormCollection(Flight $flight): array
	{
		$prices = [];
		if ($flight->fl_adults) {
			$prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_ADULT, FlightPax::getPaxId(FlightPax::PAX_ADULT), $flight->fl_adults);
		}
		if ($flight->fl_children) {
			$prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_CHILD, FlightPax::getPaxId(FlightPax::PAX_CHILD), $flight->fl_children);
		}
		if ($flight->fl_infants) {
			$prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_INFANT, FlightPax::getPaxId(FlightPax::PAX_INFANT), $flight->fl_infants);
		}
		return $prices;
	}
}