<?php

namespace sales\services\cleaner\cleaners;

use sales\model\user\entity\monitor\UserMonitor;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

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
