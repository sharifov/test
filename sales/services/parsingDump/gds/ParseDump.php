<?php

namespace sales\services\parsingDump\gds;

/**
 * Interface ParseDump
 */
interface ParseDump
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array;
}
