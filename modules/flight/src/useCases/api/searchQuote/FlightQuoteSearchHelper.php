<?php

namespace modules\flight\src\useCases\api\searchQuote;

use common\models\Airline;
use common\models\Airport;

class FlightQuoteSearchHelper
{
	public static function getAirlineLocationInfo($result)
	{
		$airlinesIata = [];
		$locationsIata = [];
		if(isset($result['results'])){
			foreach ($result['results'] as $resItem){
				if(!in_array($resItem['validatingCarrier'], $airlinesIata)){
					$airlinesIata[] = $resItem['validatingCarrier'];
				}
				foreach ($resItem['trips'] as $trip){
					foreach ($trip['segments'] as $segment){
						if(!in_array($segment['operatingAirline'], $airlinesIata)){
							$airlinesIata[] = $segment['operatingAirline'];
						}
						if(!in_array($segment['marketingAirline'], $airlinesIata)){
							$airlinesIata[] = $segment['marketingAirline'];
						}
						if(!in_array($segment['departureAirportCode'], $locationsIata)){
							$locationsIata[] = $segment['departureAirportCode'];
						}
						if(!in_array($segment['arrivalAirportCode'], $locationsIata)){
							$locationsIata[] = $segment['arrivalAirportCode'];
						}
					}
				}
			}
		}

		$airlines = Airline::getAirlinesListByIata($airlinesIata);
		$locations = Airport::getAirportListByIata($locationsIata);

		return ['airlines' => $airlines, 'locations' => $locations];
	}
}