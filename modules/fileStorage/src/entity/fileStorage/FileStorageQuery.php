<?php

namespace modules\fileStorage\src\entity\fileStorage;

use modules\fileStorage\src\entity\fileCase\FileCaseQuery;
use modules\fileStorage\src\entity\fileClient\FileClientQuery;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use modules\fileStorage\src\entity\fileUser\FileUser;

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

    public static function getByUid(string $uid): ?FileStorage
    {
        return FileStorage::find()->byUid($uid)->one();
    }

    public static function getListByUids(array $uids): array
    {
        return FileStorage::find()
        ->select([
            'fs_name as name',
            'fs_path as path',
            'fs_title as title',
            'fs_uid as uid',
            'fs_created_dt as created_dt',
            'fs_size as size',
            'fs_id as id',
            'fus_user_id as user_id',
        ])
        ->leftJoin(FileUser::tableName() . ' as user', 'fus_fs_id = fs_id')
        ->andWhere(['fs_status' => FileStorageStatus::UPLOADED])
        ->andWhere(['fs_uid' => $uids])
        ->orderBy(['id' => SORT_DESC])
        ->indexBy('id')
        ->asArray()
        ->all();

    }
}
