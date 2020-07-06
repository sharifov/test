<?php

namespace sales\services\parsingDump;



use common\models\Airport;
use common\models\QuotePrice;
use sales\services\parsingDump\lib\ParsingDump;
use sales\forms\segment\SegmentBaggageForm;

/**
 * Class BaggageService
 */
class BaggageService
{
    public string $gds;
    public array $baggageFromDump;

    public CONST TYPE_FREE = 'free';
    public CONST TYPE_PAID = 'paid';

    public CONST TYPE_LIST = [
        self::TYPE_FREE => 'Free',
        self::TYPE_PAID => 'Paid'
    ];

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
            if ($baggageSource = $this->searchByIata($segment)) {

                $baggage = [];

                if (!empty($baggageSource['paid_baggage'])) {
                    foreach ($baggageSource['paid_baggage'] as $item) {
                        $segmentBaggageForm = new SegmentBaggageForm();
                        $segmentBaggageForm->segmentIata = $segment['segmentIata'];
                        $segmentBaggageForm->type = self::TYPE_PAID;
                        $segmentBaggageForm->piece = $item['piece'];
                        $segmentBaggageForm->weight = $item['weight'];
                        $segmentBaggageForm->height = $item['height'];
                        $segmentBaggageForm->price = $item['price'];
                        $segmentBaggageForm->currency = $item['currency'];

                        $baggage[] = $segmentBaggageForm;
                    }
                }
                if (!empty($baggageSource['free_baggage'])) {
                    $segmentBaggageForm = new SegmentBaggageForm();
                    $segmentBaggageForm->segmentIata = $segment['segmentIata'];
                    $segmentBaggageForm->type = self::TYPE_FREE;
                    $segmentBaggageForm->piece = $baggageSource['free_baggage']['piece'];
                    $segmentBaggageForm->weight = $baggageSource['free_baggage']['weight'];
                    $segmentBaggageForm->height = $baggageSource['free_baggage']['height'];
                    $segmentBaggageForm->price = $baggageSource['free_baggage']['price'];
                    $segmentBaggageForm->currency = $baggageSource['free_baggage']['currency'];

                    $baggage[] = $segmentBaggageForm;
                }
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

    /**
     * @param string|null $cost
     * @return float
     */
    public static function prepareCost(?string $cost): float
    {
        $cost = $cost ?? '0.00';
        $cost = preg_replace('/[^.0-9]/', '', $cost);
        return (new QuotePrice())->roundValue($cost);
    }
}