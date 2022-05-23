<?php

namespace modules\shiftSchedule\src\services;

use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLogRepository;

/**
 * Class UserShiftScheduleLogService
 * @package modules\shiftSchedule\src\services
 *
 * @property-read UserShiftScheduleLogRepository $userShiftScheduleLogRepository
 */
class UserShiftScheduleLogService
{
    /**
     * @var UserShiftScheduleLogRepository
     */
    private UserShiftScheduleLogRepository $userShiftScheduleLogRepository;

    public function __construct(UserShiftScheduleLogRepository $userShiftScheduleLogRepository)
    {
        $this->userShiftScheduleLogRepository = $userShiftScheduleLogRepository;
    }

    public function log(int $userShiftScheduleId, string $oldAttr, string $newAttr, string $formattedAttr, ?int $userId): void
    {
        $log = new UserShiftScheduleLog();
        $log->ussl_uss_id = $userShiftScheduleId;
        $log->ussl_old_attr = $oldAttr;
        $log->ussl_new_attr = $newAttr;
        $log->ussl_formatted_attr = $formattedAttr;
        $log->ussl_created_user_id = $userId;
        $log->detachBehavior('user');

        $this->userShiftScheduleLogRepository->save($log);
    }
}
