<?php

namespace src\model\cases\useCases\cases\api\create;

use common\models\Client;
use src\entities\cases\CaseCategory;
use src\entities\cases\Cases;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\repositories\cases\CaseCategoryRepository;
use src\repositories\cases\CasesRepository;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\TransactionManager;

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

    public function handle(Command $command, ?CaseCategory $caseCategory = null): Result
    {
        $category = $caseCategory ?? $this->categoryRepository->find($command->category_id);

        /** @var Result $result */
        $result = $this->transactionManager->wrap(function () use ($command, $category) {
            $clientForm = ClientCreateForm::createWidthDefaultName();
            $clientForm->projectId = $command->project_id;
            $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
            if ($command->contact_name) {
                $clientForm->firstName = $command->contact_name;
            }

            try {
                $client = $this->clientManageService->getOrCreate(
                    [new PhoneCreateForm(['phone' => $command->contact_phone])],
                    [new EmailCreateForm(['email' => $command->contact_email])],
                    $clientForm
                );
            } catch (\DomainException $e) {
                if (!$command->chat_visitor_id || !$client = Client::find()->byProject($command->project_id)->byVisitor($command->chat_visitor_id)->one()) {
                    $client = $this->clientManageService->create($clientForm, null);
                }
            }
            if ($command->chat_visitor_id) {
                $this->clientManageService->addVisitorId($client, $command->chat_visitor_id);
            }

            $case = Cases::createByApi(
                $client->id,
                $command->project_id,
                $category->cc_dep_id,
                $command->order_uid,
                $command->subject,
                $this->processDescription($command->order_info, $command->description),
                $category->cc_id
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
