<?php

namespace src\services\cleaner\cleaners;

use common\models\Log;
use DateTime;
use Exception;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

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
        $sql = str_replace(['`', "\\\\"], ['', "\\"], $sql);
        $sql = strstr($sql, ' FROM');
        return 'DELETE' . $sql;
    }
}
