<?php

namespace modules\shiftSchedule\src\reports;

class HeatMapAgentService
{
    public const MONTH_DAY_FORMAT = 'Y-m-d';

    private array $searchResult;
    private array $eventCount = [];
    private array $eventCountByHour = [];
    private array $eventCountByMonthDay = [];

    private int $maxEventCount = 0;


    /**
     * @param array $searchResult
     */
    public function __construct(array $searchResult)
    {
        $this->searchResult = $searchResult;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $timeZone
     * @return void
     * @throws \Exception
     */
    public function mapResult(string $from, string $to, string $timeZone)
    {
        $matrix = self::generateDTMatrix($from, $to);

        foreach ($matrix as $keyYMD => $hours) {
            $this->eventCountByMonthDay[$keyYMD] = 0;

            foreach ($hours as $hour) {
                $this->eventCount[$keyYMD][$hour] = 0;
                $this->eventCountByHour[$hour] = $this->eventCountByHour[$hour] ?? 0;
                $newDate = (new \DateTime($keyYMD, new \DateTimeZone($timeZone ?: HeatMapAgentSearch::DEFAULT_TIMEZONE)))->setTime($hour, 0);
                if ($this->checkDateRangeByFilter($from, $to, $newDate, $timeZone)) {
                    foreach ($this->searchResult as $userShiftScheduleDate) {
                        if ($this->checkDateRange($userShiftScheduleDate, $newDate)) {
                            $this->eventCount[$keyYMD][$hour]++;
                            $this->eventCountByHour[$hour]++;
                            $this->eventCountByMonthDay[$keyYMD]++;
                        }
                    }
                }
                $this->setMaxEventCount($this->eventCount[$keyYMD][$hour]);
            }
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    protected static function generateDTMatrix(string $from, string $to): array
    {
        $fromDT = new \DateTime($from);
        $toDT = (new \DateTime($to))->modify('-1 day');
        $hourMap = self::generateHourMap();
        $result[$fromDT->format(static::MONTH_DAY_FORMAT)] = $hourMap;

        while ($fromDT <= $toDT) {
            $result[$fromDT->modify('+1 day')->format(static::MONTH_DAY_FORMAT)] = $hourMap;
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function generateHourMap(): array
    {
        for ($i = 0; $i < 24; $i++) {
            $result[$i] = $i;
        }
        return $result;
    }

    /**
     * @param int $value
     * @param float $fromLow
     * @param float $fromHigh
     * @param float $toLow
     * @param float $toHigh
     * @param int $decimals
     * @return string
     */
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

    /**
     * @param array $userShiftScheduleDate
     * @param \DateTime $date
     * @return bool
     */
    private function checkDateRange(array $userShiftScheduleDate, \DateTime $date): bool
    {
        if ($userShiftScheduleDate['uss_end_utc_dt']) {
            return $userShiftScheduleDate['uss_start_utc_dt'] <= $date && $userShiftScheduleDate['uss_end_utc_dt'] >= $date;
        }
        return $userShiftScheduleDate['uss_start_utc_dt'] >= $date && $userShiftScheduleDate['uss_start_utc_dt']->modify('+ 1 hour') <= $date;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $date
     * @param string $timeZone
     * @return bool
     * @throws \Exception
     */
    private function checkDateRangeByFilter(string $from, string $to, \DateTime $date, string $timeZone): bool
    {
        $fromDateTime = (new \DateTime($from, new \DateTimeZone($timeZone ?: HeatMapAgentSearch::DEFAULT_TIMEZONE)));
        $toDateTime = (new \DateTime($to, new \DateTimeZone($timeZone ?: HeatMapAgentSearch::DEFAULT_TIMEZONE)));
        return $date >= $fromDateTime && $toDateTime >= $date;
    }


    /**
     * @return int
     */
    public function getMaxEventCount(): int
    {
        return $this->maxEventCount;
    }

    /**
     * @param int $newMaxValue
     * @return void
     */
    private function setMaxEventCount(int $newMaxValue)
    {
        if ($newMaxValue > $this->maxEventCount) {
            $this->maxEventCount = $newMaxValue;
        }
    }

    /**
     * @return int
     */
    public function getMaxEventCountByHour(): int
    {
        return (int)max(array_values($this->eventCountByHour));
    }

    /**
     * @return int
     */
    public function getMaxEventCountByMonthDay(): int
    {
        return (int)max(array_values($this->eventCountByMonthDay));
    }

    /**
     * @return array
     */
    public function getEventCount(): array
    {
        return $this->eventCount;
    }

    /**
     * @return array
     */
    public function getEventCountByHour(): array
    {
        return $this->eventCountByHour;
    }

    /**
     * @return array
     */
    public function getEventCountByMonthDay(): array
    {
        return $this->eventCountByMonthDay;
    }
}
