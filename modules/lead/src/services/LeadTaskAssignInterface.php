<?php

namespace modules\lead\src\services;

use modules\taskList\src\entities\userTask\UserTask;

interface LeadTaskAssignInterface
{
    /**
     * @return false|int
     */
    public function assign();

    public function createShiftScheduleEventTask(array $userShiftSchedules, UserTask $userTask, \DateTimeImmutable $dtNowWithDelay, ?int $duration);
}
