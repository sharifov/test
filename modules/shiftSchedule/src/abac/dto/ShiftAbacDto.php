<?php

namespace modules\shiftSchedule\src\abac\dto;

class ShiftAbacDto extends \stdClass
{
    public array $formSelectUserGroups = [];
    public array $formSelectStatus = [];
    public array $formSelectScheduleType = [];

    public function setGroup(int $groupId): void
    {
        $this->formSelectUserGroups[] = $groupId;
    }

    public function setStatus(int $statusId): void
    {
        $this->formSelectStatus[] = $statusId;
    }

    public function setScheduleType(int $typeId): void
    {
        $this->formSelectScheduleType[] = $typeId;
    }
}
