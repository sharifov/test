<?php

namespace sales\parcingDump\Gds;

/**
 * Interface ParseDump
 */
interface ParseDump
{
    /**
     * @param string $string
     * @param bool $validation
     * @param array $itinerary
     * @param bool $onView
     * @return array
     */
    public function parseDump(string $string, $validation = true, &$itinerary = [], $onView = false): array;
}
