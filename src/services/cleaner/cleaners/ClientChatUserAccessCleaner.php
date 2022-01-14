<?php

namespace src\services\cleaner\cleaners;

use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\services\cleaner\BaseCleaner;
use src\services\cleaner\CleanerInterface;
use src\services\cleaner\DbCleanerService;
use src\services\cleaner\form\DbCleanerParamsForm;

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
