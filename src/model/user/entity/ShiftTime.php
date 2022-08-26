<?php

namespace src\model\user\entity;

use common\models\Employee;
use kartik\select2\ThemeDefaultAsset;
use modules\featureFlag\FFlag;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;

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

    public function __construct(?StartTime $startTime = null, ?int $workSeconds = null, ?string $timeZone = null, string $resultFormat = 'Y-m-d H:i:s')
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

        $this->startUtcDt = $startShiftTimeUTC->format($resultFormat);
        $this->endUtcDt = $endShiftTimeUTC->format($resultFormat);
        $this->endLastPeriodDt = date($resultFormat, $this->endLastPeriodTs);
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

    /**
     * @param Employee $user
     * @return ShiftTime
     */
    public static function getByUser(Employee $user): ShiftTime
    {
        /** @fflag FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE, Switch new Shift Enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE)) {
            $firstUserShiftSchedule = UserShiftScheduleQuery::getQueryForNextShiftsByUserId(
                $user->id,
                (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            )
                ->select(['user_shift_schedule.uss_start_utc_dt', 'user_shift_schedule.uss_end_utc_dt'])
                ->limit(1)
                ->asArray()
                ->one();

            if ($firstUserShiftSchedule) {
                $shiftTime = new ShiftTime();
                $shiftTime->setParam($firstUserShiftSchedule['uss_start_utc_dt'], $firstUserShiftSchedule['uss_end_utc_dt']);
                return $shiftTime;
            }
            throw new \DomainException('User id (' . $user->getId() . ') not found NextUserShiftSchedule');
        } else {
            $user->userParams->up_work_minutes = $user->userParams->up_work_minutes ?: 480;
            $startTime = $user->userParams->up_work_start_tm;
            $workSeconds = (int)$user->userParams->up_work_minutes * 60;

            if ($startTime && $workSeconds) {
                return new self(
                    new StartTime($startTime),
                    $workSeconds,
                    ($user->userParams->up_timezone ?: 'UTC')
                );
            }
            throw new \DomainException('User id (' . $user->getId() . ') has no parameters (startTime,workSeconds) set');
        }
    }

    public function setParam(string $startDate, string $endDate, string $resultFormat = 'Y-m-d H:i:s')
    {
        $startShiftTimeUTC = (new \DateTimeImmutable($startDate, new \DateTimeZone('UTC')));
        $endShiftTimeUTC = (new \DateTimeImmutable($endDate, new \DateTimeZone('UTC')));

        $this->startUtcTs = $startShiftTimeUTC->getTimestamp();
        $this->endUtcTs = $endShiftTimeUTC->getTimestamp();
        $this->endLastPeriodTs = $this->startUtcTs;

        $this->startUtcDt = $startShiftTimeUTC->format($resultFormat);
        $this->endUtcDt = $endShiftTimeUTC->format($resultFormat);
        $this->endLastPeriodDt = $startShiftTimeUTC->format($resultFormat);
    }
}
