<?php

namespace sales\services\parsingDump;

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
		$this->gds = $gds;
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
}