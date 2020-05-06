<?php

namespace sales\parcingDump\worldspanGds;

/**
 * Interface ParseDump
 */
interface ParseDump
{

    /* TODO::  */
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array;
}
