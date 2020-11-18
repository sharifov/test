<?php

namespace sales\services\cleaner\cleaners;

use common\models\ApiLog;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

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
