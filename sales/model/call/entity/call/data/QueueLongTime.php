<?php

namespace sales\model\call\entity\call\data;

/**
 * Class QueueLongTime
 *
 * @property $jobId
 * @property $departmentPhoneId
 * @property $createdJobTime
 */
class QueueLongTime
{
    public $jobId;
    public $departmentPhoneId;
    public $createdJobTime;

    public function __construct(array $data)
    {
        $this->jobId = $data['jobId'] ?? null;
        $this->departmentPhoneId = $data['departmentPhoneId'] ?? null;
        $this->createdJobTime = $data['createdJobTime'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'jobId' => $this->jobId,
            'departmentPhoneId' => $this->departmentPhoneId,
            'createdJobTime' => $this->createdJobTime,
        ];
    }

    public function reset(): void
    {
        $this->jobId = null;
        $this->departmentPhoneId = null;
        $this->createdJobTime = null;
    }

    public function isEmpty(): bool
    {
        return $this->jobId === null && $this->departmentPhoneId === null && $this->createdJobTime === null;
    }
}
