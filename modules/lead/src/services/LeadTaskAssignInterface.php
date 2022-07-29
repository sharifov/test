<?php

namespace modules\lead\src\services;

use modules\taskList\src\entities\userTask\UserTask;

interface LeadTaskAssignInterface
{
    public function assign(): void;

    public function createShiftScheduleEventTask(array $userShiftSchedules, UserTask $userTask, \DateTimeImmutable $dtNowWithDelay, ?int $duration);
}
