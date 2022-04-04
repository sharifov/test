<?php

namespace src\services\cleaner\cleaners;

use modules\requestControl\models\UserSiteActivity;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class UserSiteActivityCleaner
 * @property string $table
 * @property string $column
 */
class UserSiteActivityCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(UserSiteActivity::tableName());
        $this->setColumn('usa_created_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return UserSiteActivity::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
