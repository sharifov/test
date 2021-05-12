<?php

namespace sales\services\cleaner\cleaners;

use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\services\cleaner\BaseCleaner;
use sales\services\cleaner\CleanerInterface;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;

/**
 * Class ClientChatUserAccessCleaner
 * @property string $table
 * @property string $column
 */
class ClientChatUserAccessCleaner extends BaseCleaner implements CleanerInterface
{
    private string $table;
    private string $column;

    public function __construct()
    {
        $this->setTable(ClientChatUserAccess::tableName());
        $this->setColumn('ccua_created_dt');
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return int
     * @throws \Exception
     */
    public function runDeleteByForm(DbCleanerParamsForm $form): int
    {
        return ClientChatUserAccess::deleteAll(DbCleanerService::generateRestriction($form));
    }
}
