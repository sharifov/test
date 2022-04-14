<?php

namespace src\model\cases\useCases\cases\updateInfo;

use src\entities\cases\CaseEventLog;
use src\repositories\cases\CasesRepository;

/**
 * Class Handler
 *
 * @property CasesRepository $repository
 */
class Handler
{
    private $repository;

    public function __construct(CasesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $case = $this->repository->find($command->caseId);

        $case->updateInfo($command->depId, $command->categoryId, $command->subject, $command->description, $command->orderUid, $command->userId);

        $this->repository->save($case);
    }
}
