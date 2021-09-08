<?php

namespace sales\model\cases\useCases\cases\updateInfo;

use sales\entities\cases\CaseEventLog;
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

        $case->updateInfo($command->categoryId, $command->subject, $command->description, $command->orderUid);

        $this->repository->save($case);
        if ($case->cs_category != $command->categoryId) {
            $case->addEventLog(CaseEventLog::CASE_CATEGORY_CHANGE, 'Case category changed to ' . $case->category->cc_name . ' By: ' . ($command->username ?? 'System.'));
        }
    }
}
