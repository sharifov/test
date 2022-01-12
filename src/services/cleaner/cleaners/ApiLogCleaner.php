<?php

namespace src\services\cleaner\cleaners;

use common\models\ApiLog;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class ApiLogCleaner
 * @property string $table
 * @property string $column
 */
class ApiLogCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(ApiLog::tableName());
        $this->setColumn('al_request_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return ApiLog::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
