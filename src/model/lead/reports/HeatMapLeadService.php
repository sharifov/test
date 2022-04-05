<?php

namespace src\model\lead\reports;

/**
 * Class HeatMapLeadService
 */
class HeatMapLeadService
{
    public const MONTH_DAY_FORMAT = 'n-j';

    public static function mapResult(array $searchResult, string $from, string $to): array
    {
        $result = [];
        $matrix = self::generateDTMatrix($from, $to);

        foreach ($matrix as $keyMD => $hours) {
            foreach ($hours as $hour) {
                $result[$keyMD][$hour] = $searchResult[$keyMD . '-' . $hour]['cnt'] ?? '0';
            }
        }

        return $result;
    }

    private static function generateDTMatrix(string $from, string $to): array
    {
        $fromDT = new \DateTime($from);
        $toDT = (new \DateTime($to))->modify('-1 day');
        $hourMap = self::generateHourMap();
        $result[$fromDT->format(self::MONTH_DAY_FORMAT)] = $hourMap;

        while ($fromDT <= $toDT) {
            $result[$fromDT->modify('+1 day')->format(self::MONTH_DAY_FORMAT)] = $hourMap;
        }

        return $result;
    }

    public static function generateHourMap(): array
    {
        for ($i = 0; $i < 24; $i++) {
            $result[$i] = $i;
        }
        return $result;
    }

    public static function proportionalMap(
        int $value,
        float $fromLow,
        float $fromHigh,
        float $toLow,
        float $toHigh,
        int $decimals = 1
    ): string {
        $fromRange = $fromHigh - $fromLow;
        if (!$fromRange) {
            return 0;
        }
        $toRange = $toHigh - $toLow;
        $scaleFactor = $toRange / $fromRange;
        $tmpValue = $value - $fromLow;
        $tmpValue *= $scaleFactor;

        return number_format($tmpValue + $toLow, $decimals, '.', '');
    }

    public static function getMaxCnt(?array $searchResult = null, string $keyName = 'cnt'): int
    {
        if (!$searchResult) {
            return 0;
        }
        return (int) max(array_column($searchResult, 'cnt'));
    }
}
