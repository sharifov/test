<?php

namespace sales\services\parsingDump\lib;

/**
 * Interface ParseDump
 */
interface ParseReservationInterface
{
    /**
     * @param array $rawData
     * @return array
     */
    public function processingRow(array $rawData): array;

    /**
     * @param string $row
     * @return array
     */
    public function parseRow(string $row): array;
}
