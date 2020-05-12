<?php

namespace sales\parcingDump\Gds;

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
