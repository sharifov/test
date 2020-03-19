<?php

namespace sales\services\cases;

use sales\entities\cases\CaseStatusLog;
use sales\repositories\cases\CaseStatusLogRepository;

/**
 * Class CaseStatusLogService
 *
 * @property CaseStatusLogRepository $caseStatusLogRepository
 */
class CaseStatusLogService
{
    private $caseStatusLogRepository;

    public function __construct(CaseStatusLogRepository $caseStatusLogRepository)
    {
        $this->caseStatusLogRepository = $caseStatusLogRepository;
    }

    /**
     * @param int $caseId
     * @param int $toStatus
     * @param int|null $fromStatus
     * @param int|null $ownerId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function log(int $caseId, int $toStatus, ?int $fromStatus, ?int $ownerId, ?int $creatorId, ?string $description = ''): void
    {
        if ($previous = $this->caseStatusLogRepository->getPrevious($caseId)) {
            $previous->end();
            $this->caseStatusLogRepository->save($previous);
        }
        $log = CaseStatusLog::create(
            $caseId,
            $toStatus,
            $fromStatus,
            $creatorId,
            $ownerId,
            $description
        );
        $this->caseStatusLogRepository->save($log);
    }

}