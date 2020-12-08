<?php

namespace sales\model\cases\useCases\cases\api\create;

use common\models\Client;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CaseCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;

/**
 * Class Handler
 *
 * @property CaseCategoryRepository $categoryRepository
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
        CaseCategoryRepository $categoryRepository,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        TransactionManager $transactionManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->transactionManager = $transactionManager;
    }

    public function handle(Command $command): Result
    {
        $category = $this->categoryRepository->find($command->category_id);

        /** @var Result $result */
        $result = $this->transactionManager->wrap(function () use ($command, $category) {

            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $command->project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_CASE;

            $client = $this->clientManageService->getOrCreate(
                [new PhoneCreateForm(['phone' => $command->contact_phone])],
                [new EmailCreateForm(['email' => $command->contact_email])],
                $clientForm
            );

            $case = Cases::createByApi(
                $client->id,
                $command->project_id,
                $category->cc_dep_id,
                $command->order_uid,
                $command->subject,
                $this->processDescription($command->order_info, $command->description),
                $command->category_id
            );

            $this->casesRepository->save($case);

            return new Result($case->cs_gid, $client->uuid, $case->cs_id);
        });

        return $result;
    }

    private function processDescription(array $orderInfo, ?string $description): ?string
    {
        $result = '';
        foreach ($orderInfo as $key => $value) {
            $result .= $key . ': ' . $value . PHP_EOL;
        }
        $result .= $description;
        return $result ?: null;
    }
}
