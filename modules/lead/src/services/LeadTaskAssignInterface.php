<?php

namespace modules\lead\src\services;

use modules\taskList\src\entities\userTask\UserTask;

interface LeadTaskAssignInterface
{
    /**
     * @return int|null
     */
    public function assign(): ?int;

    public function createShiftScheduleEventTask(array $userShiftSchedules, UserTask $userTask, \DateTimeImmutable $dtNowWithDelay, ?int $duration);
}
