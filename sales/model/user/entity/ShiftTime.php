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
    private const SECONDS_ON_DAY = 86400;

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

        $currentTimeUser = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone($timeZone));

        $currentTimeUserSeconds = (int)$currentTimeUser->format('H') * 3600 + (int)$currentTimeUser->format('i') * 60 + (int)$currentTimeUser->format('s');

        $endTimeSeconds = $startTime->toSeconds() + $workSeconds;
        if ($endTimeSeconds > self::SECONDS_ON_DAY) {
            if ($currentTimeUserSeconds > ($endTimeSeconds - self::SECONDS_ON_DAY)) {
                $startShiftTimeUser = $currentTimeUser->setTime($startTime->hour, $startTime->minute, $startTime->second);
            } else {
                $startShiftTimeUser = $currentTimeUser->setTime($startTime->hour, $startTime->minute, $startTime->second)->modify('-1 day');
            }
        } else {
            if ($currentTimeUserSeconds <= $endTimeSeconds) {
                $startShiftTimeUser = $currentTimeUser->setTime($startTime->hour, $startTime->minute, $startTime->second);
            } else {
                $startShiftTimeUser = $currentTimeUser->setTime($startTime->hour, $startTime->minute, $startTime->second)->modify('+1 day');
            }
        }

        $startShiftTimeUTC = $startShiftTimeUser->setTimezone(new \DateTimeZone('UTC'));
        $endShiftTimeUTC = $startShiftTimeUTC->add(new \DateInterval('PT' . $workSeconds . 'S'));

        $this->startUtcTs = $startShiftTimeUTC->getTimestamp();
        $this->endUtcTs = $endShiftTimeUTC->getTimestamp();
        $this->endLastPeriodTs = $this->endUtcTs - self::SECONDS_ON_DAY;

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
