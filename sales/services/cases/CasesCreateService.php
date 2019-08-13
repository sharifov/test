<?php

namespace sales\services\cases;

use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;

/**
 * Class CasesCreateService
 *
 * @property CasesRepository $casesRepository
 */
class CasesCreateService
{

    private $casesRepository;

    /**
     * CasesCreateService constructor.
     * @param CasesRepository $casesRepository
     */
    public function __construct(CasesRepository $casesRepository)
    {
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param int $clientId
     * @param int $callId
     * @param int $projectId
     * @param int|null $depId
     * @return Cases
     */
    public function createByCall(int $clientId, int $callId, int $projectId, ?int $depId): Cases
    {
        $case = Cases::createByCall(
            $clientId,
            $callId,
            $projectId,
            $depId
        );
        $this->casesRepository->save($case);
        return $case;
    }
}