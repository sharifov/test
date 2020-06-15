<?php

namespace sales\services\parsingDump;

use common\models\QuotePrice;
use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class PricingService
 */
class PricingService
{
    public string $gds;

    /**
     * ReservationService constructor.
     * @param string $gds
     */
    public function __construct(string $gds)
	{
		$this->gds = ParsingDump::setGdsForParsing($gds);
	}

    /**
     * @param string $string
     * @return array
     */
    public function formattingForQuote(string $string): array
    {
        $result = [];
        $parserClass = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_PRICING);

        if ($parsedDump = $parserClass->parseDump($string)) {
            $result['validating_carrier'] = $parsedDump['validating_carrier'];
            foreach ($parsedDump['prices'] as $price) {
                if (isset($result['prices'][$price['type']])) {
                    $result['prices'][$price['type']] = [
                        'fare' => $result['prices'][$price['type']]['fare'] + $price['fare'],
                        'taxes' => $result['prices'][$price['type']]['taxes'] + $price['taxes'],
                    ];
                } else {
                    $result['prices'][$price['type']] = [
                        'fare' => $price['fare'],
                        'taxes' => $price['taxes'],
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @param string|null $source
     * @return string
     */
    public static function passengerTypeMapping(?string $source): string
    {
        switch ($source) {
            case 'ADT': case 'JCB': case 'PFA': case 'ITX': case 'JWZ': case 'WEB':
                $result = QuotePrice::PASSENGER_ADULT;
                break;
            case 'CHD': case 'CNN': case 'JNN':case 'CBC': case 'INN': case 'PNN': case 'JWC': case 'UNN':
                $result = QuotePrice::PASSENGER_CHILD;
                break;
            case 'INF': case 'INS': case 'JNS':case 'CBI': case 'JNF': case 'PNF': case 'ITF': case 'ITS':
                $result = QuotePrice::PASSENGER_INFANT;
                break;
            default:
                $result = QuotePrice::PASSENGER_ADULT;
        }
        return $result;
    }
}