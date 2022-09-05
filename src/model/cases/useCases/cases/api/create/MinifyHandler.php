<?php

namespace src\model\cases\useCases\cases\api\create;

use src\entities\cases\CaseCategory;
use src\entities\cases\Cases;
use src\repositories\cases\CaseCategoryRepository;
use src\repositories\cases\CasesRepository;

/**
 * Class MinifyHandler
 *
 * @property CaseCategoryRepository $categoryRepository
 * @property CasesRepository $casesRepository
 */
class MinifyHandler
{
    private CaseCategoryRepository $categoryRepository;
    private CasesRepository $casesRepository;

    public function __construct(
        CaseCategoryRepository $categoryRepository,
        CasesRepository $casesRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @throws \Throwable
     */
    public function handle(MinifyCommand $command, ?CaseCategory $caseCategory = null): MinifyResult
    {
        $category = $caseCategory ?? $this->categoryRepository->find($command->category_id);

        $case = Cases::createByApiMinify(
            $command->project_id,
            $category->cc_dep_id,
            $command->subject,
            $command->description,
            $category->cc_id,
            $command->is_automate
        );

        $this->casesRepository->save($case);

        return new MinifyResult($case->cs_gid, $case->cs_id);
    }
}
