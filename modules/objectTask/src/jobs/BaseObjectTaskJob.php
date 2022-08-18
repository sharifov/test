<?php

namespace modules\objectTask\src\jobs;

use common\components\jobs\BaseJob;

class BaseObjectTaskJob extends BaseJob
{
    protected array $commandConfig;
    protected string $objectTaskID;

    public function __construct(array $jobConfig, string $objectTaskID, ?float $timeStart = null, $config = [])
    {
        $this->commandConfig = $jobConfig;
        $this->objectTaskID = $objectTaskID;

        parent::__construct($timeStart, $config);
    }

    public function getCommandConfig(): array
    {
        return $this->commandConfig;
    }

    public function getObjectTaskId(): string
    {
        return $this->objectTaskID;
    }
}
