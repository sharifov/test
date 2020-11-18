<?php

namespace sales\services\cleaner\cleaners;

use common\models\GlobalLog;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

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
