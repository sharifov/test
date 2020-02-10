<?php

namespace modules\qaTask\src\services;

use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLogRepository;

/**
 * Class QaTaskStatusLogService
 *
 * @property QaTaskStatusLogRepository $repository
 */
class QaTaskStatusLogService
{
    private $repository;

    public function __construct(QaTaskStatusLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        if ($previous = $this->repository->getPrevious($dto->taskId)) {
            $previous->end();
            $this->repository->save($previous);
        }
        $log = QaTaskStatusLog::create($dto);
        $this->repository->save($log);
    }
}
