<?php

namespace sales\model\user\entity;

/**
 * Class ShiftTime
 *
 * @property int|null $startUtcTs
 * @property int|null $endUtcTs
 * @property int|null $endLastPeriodTs
 * @property string|null $startUtcDt
 * @property string|null $endUtcDt
 * @property string|null $endLastPeriodDt
 */
class ShiftTime
{
    public $startUtcTs;
    public $endUtcTs;
    public $endLastPeriodTs;
    public $startUtcDt;
    public $endUtcDt;
    public $endLastPeriodDt;

    public function __construct(?StartTime $startTime = null, ?int $workSeconds = null, ?string $timeZone = null)
    {
        if ($startTime === null || $workSeconds === null || $timeZone === null) {
            return;
        }

        $currentTimeUTC = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $startShiftTimeUTC = (new \DateTimeImmutable('now', new \DateTimeZone($timeZone)))
            ->setTime($startTime->hour, $startTime->minute, $startTime->second)
            ->setTimezone(new \DateTimeZone('UTC'));

        $endShiftTimeUTC = $startShiftTimeUTC->add(new \DateInterval('PT' . $workSeconds . 'S'));

        $endShiftMinutes = (int)($endShiftTimeUTC->format('H')) * 60 + (int)$endShiftTimeUTC->format('i');
        $currentMinutes = (int)($currentTimeUTC->format('H')) * 60 + (int)$currentTimeUTC->format('i');

        if (
            ($currentMinutes >= 0 && $endShiftMinutes >= $currentMinutes)
            && $startShiftTimeUTC->format('d') !== $endShiftTimeUTC->format('d')
        ) {
            $startShiftTimeUTC = $startShiftTimeUTC->modify('-1 day');
            $endShiftTimeUTC = $endShiftTimeUTC->modify('-1 day');
        }

        $this->startUtcTs = $startShiftTimeUTC->getTimestamp();
        $this->endUtcTs = $endShiftTimeUTC->getTimestamp();
        $this->endLastPeriodTs = $this->endUtcTs - (24 * 60 * 60);

        $this->startUtcDt = $startShiftTimeUTC->format('Y-m-d H:i:s');
        $this->endUtcDt = $endShiftTimeUTC->format('Y-m-d H:i:s');
        $this->endLastPeriodDt = date('Y-m-d H:i:s', $this->endLastPeriodTs);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->startUtcTs === null
            && $this->endUtcTs === null
            && $this->endLastPeriodTs === null
            && $this->startUtcDt === null
            && $this->endUtcDt === null
            && $this->endLastPeriodDt === null;
    }
}
