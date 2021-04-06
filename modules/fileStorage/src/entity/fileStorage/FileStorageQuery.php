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

    /**
     * @param int $orderId
     * @return FileStorage[]|null
     */
    public static function getByOrderId(int $orderId): ?array
    {
        return FileStorage::find()
            ->innerJoinWith('fileOrders')
            ->where(['fo_or_id' => $orderId])
            ->orderBy(['fs_id' => SORT_DESC])
            ->all();
    }
}
