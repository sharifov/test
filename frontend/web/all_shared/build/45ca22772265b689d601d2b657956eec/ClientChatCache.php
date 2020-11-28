<?php

namespace frontend\widgets\clientChat;

use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class ClientChatCache
{
    private const PREFIX_KEY = '_cc_access_key_';
    private const PREFIX_TAG = '_cc_access_tag_';

    public static function getCache(): CacheInterface
    {
        return \Yii::$app->cache;
    }

    public static function invalidate(int $userId): void
    {
        TagDependency::invalidate(self::getCache(), self::getTags($userId));
    }

    public static function getKey(int $userId): string
    {
        return self::PREFIX_KEY . $userId;
    }

    public static function getTags(int $userId): string
    {
        return self::PREFIX_TAG . $userId;
    }
}
