<?php

namespace modules\shiftSchedule\src\abac\dto;

class ShiftAbacDto extends \stdClass
{
    public array $formSelectUserGroups = [];
    public array $formSelectStatus = [];
    public array $formSelectScheduleType = [];
    public array $formSelectRequestStatus = [];
    public bool $isEventOwner = false;

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

    public function setIsEventOwner(bool $isOwner): void
    {
        $this->isEventOwner = $isOwner;
    }

    /**
     * Setting Request status (ShiftScheduleRequest)
     * @param int $requestStatusId
     * @return void
     */
    public function setRequestStatus(int $requestStatusId): void
    {
        $this->formSelectRequestStatus[] = $requestStatusId;
    }
}
