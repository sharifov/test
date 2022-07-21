<?php

namespace src\model\cases\useCases\cases\addBookingId;

use src\repositories\cases\CasesRepository;

/**
 * Class Handler
 *
 * @property CasesRepository $repository
 */
class AddBookingIdHandler
{
    private CasesRepository $repository;

    public function __construct(CasesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $case = $this->repository->find($command->caseId);

        $case->updateBookingId($command->orderUid, $command->userId);

        $this->repository->save($case);
    }
}
