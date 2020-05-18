<?php

namespace sales\services\parsingDump\worldSpan;

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
