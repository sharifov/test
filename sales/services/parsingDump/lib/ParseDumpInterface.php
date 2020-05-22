<?php

namespace sales\services\parsingDump\lib;

/**
 * Interface ParseDump
 */
interface ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array;
}
