<?php

namespace sales\services\cleaner\cleaners;

use common\models\Notifications;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class NotificationsCleaner
 * @property string $table
 * @property string $column
 */
class NotificationsCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(Notifications::tableName());
        $this->setColumn('n_created_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return Notifications::deleteAll(DbCleanerService::generateRestriction($form));
    }
}