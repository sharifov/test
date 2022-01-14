<?php

namespace src\services\cleaner\cleaners;

use src\model\user\entity\monitor\UserMonitor;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class UserMonitorCleaner
 * @property string $table
 * @property string $column
 */
class UserMonitorCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(UserMonitor::tableName());
        $this->setColumn('um_start_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return UserMonitor::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
