<?php

namespace sales\services\parsingDump;



use common\models\Airport;
use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class BaggageService
 */
class BaggageService
{
    public string $gds;
    public array $baggageFromDump;

    /**
     * @param string $gds
     */
    public function __construct(string $gds)
	{
		$this->gds = ParsingDump::setGdsForParsing($gds);
	}

    /**
     * @param $dump
     * @return $this
     */
    public function setBaggageFromDump($dump): BaggageService
    {
        $parserBaggage = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_BAGGAGE);
        $this->baggageFromDump = $parserBaggage->parseDump($dump);
        return $this;
    }

    /**
     * @param array $segments
     * @return array
     */
    public function attachBaggageToSegments(array $segments): array
    {
        foreach ($segments as $key => $segment) {
            if ($baggage = $this->searchByIata($segment['departure_airport_iata'], $segment['arrival_airport_iata'])) {
                $segments['baggage'] = $baggage;
            }
        }
        return $segments;
    }

    /**
     * @param string $departureIata
     * @param string $arrivalIata
     * @return null|array
     */
    public function searchByIata(string $departureIata, string $arrivalIata): ?array
    {
        $segmentIata = $departureIata . $arrivalIata;

        foreach ($this->baggageFromDump as $key => $item) {
            if ($item['segment'] === $segmentIata) {
                $baggage = $this->baggageFromDump['baggage'][$key];
                break;
            }
            if ((new Airport())->getCityByIata($departureIata) === (new Airport())->getCityByIata($arrivalIata)) {
                $baggage = $this->baggageFromDump['baggage'][$key];
                break;
            }
        }
        return $baggage ?? null;
    }
}