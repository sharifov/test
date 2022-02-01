<?php

namespace src\services\cleaner\cleaners;

use common\models\Notifications;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class LeadPoorProcessingLogCleaner
 * @property string $table
 * @property string $column
 */
class LeadPoorProcessingLogCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(LeadPoorProcessingLog::tableName());
        $this->setColumn('lppl_created_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return LeadPoorProcessingLog::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
