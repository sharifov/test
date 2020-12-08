<?php

namespace sales\services\cleaner\cleaners;

use frontend\models\UserSiteActivity;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

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
