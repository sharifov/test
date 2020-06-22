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
            if ($baggage = $this->searchByIata($segment)) {
                $segments[$key]['baggage'] = $baggage;
            }
        }
        return $segments;
    }

    /**
     * @param array $segment
     * @return array|null
     */
    public function searchByIata(array $segment): ?array
    {
        $segmentIata = $segment['departureAirport'] . $segment['arrivalAirport'];

        if (!empty($this->baggageFromDump['baggage'])) {
            foreach ($this->baggageFromDump['baggage'] as $key => $baggageItem) {
                if ($baggageItem['segment'] === $segmentIata) {
                    $baggage = $this->baggageFromDump['baggage'][$key];
                    unset($this->baggageFromDump['baggage'][$key]);
                    break;
                }
                $baggageDeparture = substr($baggageItem['segment'], 0, 3);
                $baggageArrival = substr($baggageItem['segment'], 3, 3);

                if (
                    ($segment['departureCity']->city === Airport::getCityByIata($baggageDeparture))
                    &&
                    ($segment['arrivalCity']->city === Airport::getCityByIata($baggageArrival))
                ) {
                    $baggage = $this->baggageFromDump['baggage'][$key];
                    unset($this->baggageFromDump['baggage'][$key]);
                    break;
                }
            }
        }
        return $baggage ?? null;
    }
}