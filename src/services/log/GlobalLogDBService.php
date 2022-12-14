<?php

namespace src\services\log;

use src\entities\log\GlobalLog;
use src\repositories\log\GlobalLogRepository;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;

/**
 * Class GlobalLogDBService
 * @package src\services\log
 *
 * @property GlobalLogRepository $globalLogRepository
 */
class GlobalLogDBService implements GlobalLogInterface
{
    /**
     * @var GlobalLogRepository
     */
    private $globalLogRepository;

    /**
     * GlobalLogsService constructor.
     * @param GlobalLogRepository $globalLogRepository
     */
    public function __construct(GlobalLogRepository $globalLogRepository)
    {
        $this->globalLogRepository = $globalLogRepository;
    }

    /**
     * @param LogDTO $data
     */
    public function log(LogDTO $data): void
    {
        $log = GlobalLog::create(
            $data->glModel,
            $data->glObjectId,
            $data->glAppId,
            $data->glAppUserId,
            $data->glOldAttr,
            $data->glNewAttr,
            $data->glFormattedAttr,
            $data->glActionType,
            $data->glCreatedAt
        );
        $this->globalLogRepository->save($log);
    }
}
