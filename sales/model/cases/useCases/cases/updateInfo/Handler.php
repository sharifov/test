<?php

namespace sales\model\cases\useCases\cases\updateInfo;

use sales\repositories\cases\CasesRepository;

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

        $case->updateInfo($command->category, $command->subject, $command->description, $command->orderUid);

        $this->repository->save($case);
    }
}
