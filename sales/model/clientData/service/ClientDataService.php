<?php

namespace sales\model\clientData\service;

use sales\helpers\ErrorsToStringHelper;
use sales\model\clientData\entity\ClientData;
use sales\model\clientData\repository\ClientDataRepository;
use sales\model\clientDataKey\service\ClientDataKeyService;

/**
 * Class ClientDataService
 */
class ClientDataService
{
    public static function setValue(int $clientId, string $key, string $value): ClientData
    {
        if (!$keyId = ClientDataKeyService::getIdByKeyCache($key)) {
            throw new \RuntimeException('ClientDataKey not found (' . $key . ')');
        }

        if (!$clientData = self::findByClientAndKeyId($clientId, $keyId)) {
            $clientData = ClientData::create($clientId, $keyId, $value);
        }
        $clientData->cd_field_value = $value;

        if (!$clientData->validate()) {
            throw new \RuntimeException('ClientData not saved. ' .
                ErrorsToStringHelper::extractFromModel($clientData, ' '));
        }

        $clientDataRepository = new ClientDataRepository($clientData);
        $clientDataRepository->save();
        return $clientDataRepository->getModel();
    }

    public static function findByClientAndKeyId(int $clientId, int $keyId): ?ClientData
    {
        return ClientData::find()
            ->where(['cd_key_id' => $keyId])
            ->andWhere(['cd_client_id' => $clientId])
            ->one();
    }

    public static function existByClientKeyIdValue(int $clientId, string $key, string $value): bool
    {
        if (!$keyId = ClientDataKeyService::getIdByKeyCache($key, null)) {
            throw new \RuntimeException('ClientDataKey not found (' . $key . ')');
        }
        return ClientData::find()
            ->where(['cd_key_id' => $keyId])
            ->andWhere(['cd_client_id' => $clientId])
            ->andWhere(['cd_field_value' => $value])
            ->exists();
    }
}
