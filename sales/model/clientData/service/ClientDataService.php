<?php

namespace sales\model\clientData\service;

use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\clientData\entity\ClientData;
use sales\model\clientData\repository\ClientDataRepository;
use sales\model\clientDataKey\service\ClientDataKeyService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ClientDataService
 */
class ClientDataService
{
    public static function createFromApi(array $clientDatas, int $clientId): array
    {
        $inserted = $warnings = [];
        foreach ($clientDatas as $value) {
            try {
                $clientData = self::setValue(
                    $clientId,
                    (string) ArrayHelper::getValue($value, 'field_key'),
                    (string) ArrayHelper::getValue($value, 'field_value')
                );
                $inserted[] = [
                    'key' => $clientData->cdKey->cdk_key,
                    'value' => $clientData->cd_field_value,
                ];
            } catch (\Throwable $throwable) {
                Yii::warning(AppHelper::throwableLog($throwable), 'ClientDataService:createFromApi');
                $warnings[] = $throwable->getMessage();
            }
        }
        return [$inserted, $warnings];
    }

    public static function setValue(int $clientId, string $key, string $value, bool $updateMode = true): ClientData
    {
        if (!$keyId = ClientDataKeyService::getIdByKeyCache($key)) {
            throw new \RuntimeException('ClientDataKey not found (' . $key . ')');
        }

        if (!$clientData = self::findByClientAndKeyId($clientId, $keyId)) {
            $clientData = ClientData::create($clientId, $keyId, $value);
        } elseif (!$updateMode) {
            throw new \RuntimeException('ClientData already exist. Key(' .
                $key . ')' . ' clientID(' . $clientId . ')');
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
