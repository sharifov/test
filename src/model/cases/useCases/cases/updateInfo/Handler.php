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

        if (($case->cs_category_id != $command->categoryId) && $case->category) {
            $case->addEventLog(CaseEventLog::CASE_CATEGORY_CHANGE, 'Case category changed to ' . $case->category->cc_name . ' By: ' . ($command->username ?? 'System.'));
        }

        if (($case->cs_dep_id != $command->depId) && $case->department) {
            $case->addEventLog(CaseEventLog::CASE_DEPARTMENT_CHANGE, 'Case department changed to ' . $case->department->dep_name . ' By: ' . ($command->username ?? 'System.'));
        }

        $case->updateInfo($command->depId, $command->categoryId, $command->subject, $command->description, $command->orderUid);

        $this->repository->save($case);
    }
}
