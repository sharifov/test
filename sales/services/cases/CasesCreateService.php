<?php

namespace sales\services\cases;

use sales\entities\cases\Cases;
use sales\forms\cases\CasesCreateByWebForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class CasesCreateService
 *
 * @property CasesRepository $casesRepository
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transaction
 */
class CasesCreateService
{

    private $casesRepository;
    private $clientManageService;
    private $transaction;

    /**
     * CasesCreateService constructor.
     * @param CasesRepository $casesRepository
     * @param ClientManageService $clientManageService
     * @param TransactionManager $transaction
     */
    public function __construct(
        CasesRepository $casesRepository,
        ClientManageService $clientManageService,
        TransactionManager $transaction
    )
    {
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->transaction = $transaction;
    }

    /**
     * @param CasesCreateByWebForm $form
     * @return Cases
     * @throws \Exception
     */
    public function createByWeb(CasesCreateByWebForm $form): Cases
    {
        $case = $this->transaction->wrap(function () use ($form) {

            $client = $this->clientManageService->getOrCreate([new PhoneCreateForm(['phone' => $form->clientPhone])]);
            $case = Cases::createByWeb(
                $form->projectId,
                $form->category,
                $client->id,
                $form->depId,
                $form->subject,
                $form->description
            );
            $this->casesRepository->save($case);
            return $case;

        });

        return $case;
    }

    /**
     * @param PhoneCreateForm[] $clientPhones
     * @param int $callId
     * @param int $projectId
     * @param int|null $depId
     * @return Cases
     * @throws \Exception
     */
    public function createByCall(array $clientPhones, int $callId, int $projectId, ?int $depId): Cases
    {
        $case = $this->transaction->wrap(function () use ($clientPhones, $callId, $projectId, $depId) {

            $client = $this->clientManageService->getOrCreate($clientPhones);
            $case = Cases::createByCall(
                $client->id,
                $callId,
                $projectId,
                $depId
            );
            $this->casesRepository->save($case);
            return $case;

        });

        return $case;
    }

    /**
     * @param PhoneCreateForm[] $clientPhones
     * @param int $callId
     * @param int $projectId
     * @param int|null $depId
     * @return Cases
     * @throws \Exception
     */
    public function getOrCreateByCall(array $clientPhones, int $callId, int $projectId, ?int $depId): Cases
    {

        $case = $this->transaction->wrap(function () use ($clientPhones, $callId, $projectId, $depId) {

            $client = $this->clientManageService->getOrCreate($clientPhones);
            if (!$case = $this->casesRepository->getByClientProjectDepartment($client->id, $projectId, $depId)) {
                \Yii::info('Not found case:  ' . VarDumper::dumpAsString([$client->id, $projectId, $depId]), 'info\getByClientProjectDepartment');
                $case = Cases::createByCall(
                    $client->id,
                    $callId,
                    $projectId,
                    $depId
                );
                $this->casesRepository->save($case);
            } else {
                \Yii::info('Find case: ' . $case->cs_id . ' - ' . VarDumper::dumpAsString([$client->id, $projectId, $depId]), 'info\getByClientProjectDepartment');
            }
            return $case;

        });

        return $case;
    }
}
