<?php

namespace sales\model\cases\useCases\cases\api\create;

use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CasesCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;

/**
 * Class Handler
 *
 * @property CasesCategoryRepository $categoryRepository
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 * @property TransactionManager $transactionManager
 */
class Handler
{
    private $categoryRepository;
    private $clientManageService;
    private $casesRepository;
    private $transactionManager;

    public function __construct(
        CasesCategoryRepository $categoryRepository,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        TransactionManager $transactionManager
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->transactionManager = $transactionManager;
    }

    public function handle(Command $command): Result
    {
        $category = $this->categoryRepository->findByKey($command->category);

        /** @var Result $result */
        $result = $this->transactionManager->wrap(function () use ($command, $category) {

            $client = $this->clientManageService->getOrCreate(
                [new PhoneCreateForm(['phone' => $command->phone])],
                [new EmailCreateForm(['email' => $command->email])]
            );

            $case = Cases::createByApi(
                $client->id,
                $command->project_id,
                $category->cc_dep_id,
                $command->subject,
                $command->description
            );

            $this->casesRepository->save($case);

            return new Result($case->cs_gid, $client->uuid);

        });

        return $result;
    }
}
