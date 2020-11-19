<?php

namespace sales\services\cleaner\cleaners;

use common\models\Call;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class CallCleaner
 * @property string $table
 * @property string $column
 */
class CallCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(Call::tableName());
        $this->setColumn('c_created_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return Call::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
