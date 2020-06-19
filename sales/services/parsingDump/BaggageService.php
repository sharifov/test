<?php

namespace sales\services\parsingDump;

use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class BaggageService
 */
class BaggageService
{
    public string $gds;

    /**
     * @param string $gds
     */
    public function __construct(string $gds)
	{
		$this->gds = ParsingDump::setGdsForParsing($gds);
	}


}