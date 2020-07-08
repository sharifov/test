<?php

namespace frontend\widgets\notification;

use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class NotificationCache
{
	private const PREFIX_KEY = 'notification_';
	private const PREFIX_TAG = 'notification_';
	private const PREFIX_CC_KEY = 'notification_cc_key_';
	private const PREFIX_CC_TAG = 'notification_cc_tag_';

	public static function getCache(): CacheInterface
	{
		return \Yii::$app->cache;
	}

	public static function invalidate(int $userId): void
	{
		TagDependency::invalidate(self::getCache(), self::getTags($userId));
	}

	public static function invalidateCc(int $userId): void
	{
		TagDependency::invalidate(self::getCache(), self::getClientChatTags($userId));
	}

	public static function getKey(int $userId): string
	{
		return self::PREFIX_KEY . $userId;
	}

	public static function getTags(int $userId): string
	{
		return self::PREFIX_TAG . $userId;
	}

	public static function getClientChatKey(int $userId): string
	{
		return self::PREFIX_CC_KEY . $userId;
	}

	public static function getClientChatTags(int $userId): string
	{
		return self::PREFIX_CC_TAG . $userId;
	}
}
