<?php

namespace sales\services\cases;

use sales\entities\cases\CasesStatusLog;
use sales\repositories\cases\CasesStatusLogRepository;

/**
 * Class CasesStatusLogService
 *
 * @property CasesStatusLogRepository $casesStatusLogRepository
 */
class CasesStatusLogService
{

    private $casesStatusLogRepository;

    /**
     * @param CasesStatusLogRepository $casesStatusLogRepository
     */
    public function __construct(CasesStatusLogRepository $casesStatusLogRepository)
    {
        $this->casesStatusLogRepository = $casesStatusLogRepository;
    }

    /**
     * @param int $caseId
     * @param int $toStatus
     * @param int|null $fromStatus
     * @param int|null $ownerId
     * @param int|null $createdUserId
     * @param string|null $description
     */
    public function log(int $caseId, int $toStatus, ?int $fromStatus, ?int $ownerId, ?int $createdUserId, ?string $description = ''): void
    {
        if ($previous = $this->casesStatusLogRepository->getPrevious($caseId)) {
            $previous->end();
            $this->casesStatusLogRepository->save($previous);
        }
        $log = CasesStatusLog::create(
            $caseId,
            $toStatus,
            $fromStatus,
            $createdUserId,
            $ownerId,
            $description
        );
        $this->casesStatusLogRepository->save($log);
    }

}