<?php

namespace modules\fileStorage\src\entity\fileClient;

class FileClientQuery
{
    public static function getClient(int $fileId): ?int
    {
        $client = FileClient::find()->select(['fcl_client_id'])->byFile($fileId)->asArray()->one();
        return $client ? (int)$client['fcl_client_id'] : null;
    }
}
