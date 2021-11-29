<?php

namespace sales\model\clientDataKey\service;

use sales\model\clientDataKey\entity\ClientDataKey;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

/**
 * Class ClientDataKeyService
 */
class ClientDataKeyService
{
    public static function getList(?bool $isEnable = true): array
    {
        $query = ClientDataKey::find()->select(['cdk_id', 'cdk_key']);
        if (is_bool($isEnable)) {
            $query->where(['cdk_enable' => $isEnable]);
        }
        return ArrayHelper::map(
            $query->all(),
            'cdk_id',
            'cdk_key'
        );
    }

    public static function getListCache(?bool $isEnable = true, int $duration = ClientDataKeyDictionary::CACHE_DURATION): array
    {
        return Yii::$app->cache->getOrSet(ClientDataKeyDictionary::CACHE_TAG, static function () use ($isEnable) {
            return self::getList($isEnable);
        }, $duration, new TagDependency([
            'tags' => ClientDataKeyDictionary::CACHE_TAG,
        ]));
    }

    public static function getIdByKeyCache(string $key, ?bool $isEnable = true, int $duration = ClientDataKeyDictionary::CACHE_DURATION): ?int
    {
        return Yii::$app->cache->getOrSet(ClientDataKeyDictionary::CACHE_TAG . $key, static function () use ($key, $isEnable) {
            return self::getIdByKey($key, $isEnable);
        }, $duration, new TagDependency([
            'tags' => ClientDataKeyDictionary::CACHE_TAG,
        ]));
    }

    public static function getIdByKey(string $key, ?bool $isEnable = true): ?int
    {
        $query = ClientDataKey::find()
            ->select(['cdk_id'])
            ->where(['cdk_key' => $key]);

        if ($isEnable !== null) {
            $query->andWhere(['cdk_enable' => $isEnable]);
        }
        $result = $query->scalar();
        return $result ? (int) $result : null;
    }

    public static function getKeyByIdCache(int $id, ?bool $isEnable = true, int $duration = ClientDataKeyDictionary::CACHE_DURATION): ?string
    {
        return Yii::$app->cache->getOrSet(ClientDataKeyDictionary::CACHE_TAG . $id, static function () use ($id, $isEnable) {
            return self::getKeyById($id, $isEnable);
        }, $duration, new TagDependency([
            'tags' => ClientDataKeyDictionary::CACHE_TAG,
        ]));
    }

    public static function getKeyById(int $id, ?bool $isEnable = true): ?string
    {
        $query = ClientDataKey::find()
            ->select(['cdk_key'])
            ->where(['cdk_id' => $id]);

        if ($isEnable !== null) {
            $query->andWhere(['cdk_enable' => $isEnable]);
        }
        $result = $query->scalar();

        return $result ? (string) $result : null;
    }
}
