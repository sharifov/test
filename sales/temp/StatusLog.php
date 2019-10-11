<?php

namespace sales\temp;

/**
 * Class StatusLog
 *
 * @property $oldStatus
 * @property $newStatus
 * @property $oldOwner
 * @property $newOwner
 * @property $date
 * @property $createdUserId
 * @property $description
 * @property $endDate;
 * @property $duration;
 */
class StatusLog
{
    public $oldStatus;

    public $newStatus;

    public $oldOwner;

    public $newOwner;

    public $createdUserId;

    public $date;

    public $description;

    public $endDate;

    public $duration;

    public function __construct($oldStatus, $newStatus, $oldOwner, $newOwner, $date, $createdUserId = null)
    {
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
        $this->date = $date;
        $this->createdUserId = $createdUserId;
    }
}