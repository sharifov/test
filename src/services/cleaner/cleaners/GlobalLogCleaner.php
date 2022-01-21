<?php

namespace src\services\cleaner\cleaners;

use common\models\GlobalLog;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class GlobalLogCleaner
 * @property string $table
 * @property string $column
 */
class GlobalLogCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(GlobalLog::tableName());
        $this->setColumn('gl_created_at');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return GlobalLog::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
