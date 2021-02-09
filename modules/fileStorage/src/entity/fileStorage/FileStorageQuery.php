<?php

namespace modules\fileStorage\src\entity\fileStorage;

use modules\fileStorage\src\entity\fileCase\FileCaseQuery;
use modules\fileStorage\src\entity\fileClient\FileClientQuery;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;

class FileStorageQuery
{
    public static function getRelations(int $fileId): FileStorageRelations
    {
        $leadId = FileLeadQuery::getLead($fileId);
        $caseId = FileCaseQuery::getCase($fileId);
        $clientId = FileClientQuery::getClient($fileId);
        return new FileStorageRelations($leadId, $caseId, $clientId);
    }
}
