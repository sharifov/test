<?php

namespace sales\services\cleaner\cleaners;

use common\models\Log;
use DateTime;
use Exception;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class LogCleaner
 * @property string $table
 * @property string $column
 */
class LogCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(Log::tableName());
        $this->setColumn('log_time');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return Log::deleteAll(DbCleanerService::generateRestrictionTimestamp($form));
    }

    public static function replaceSelectToDelete(string $sql): string
    {
        $sql = str_replace('`', '', $sql);
        $sql = strstr($sql, ' FROM');
        return 'DELETE' . $sql;
    }
}
