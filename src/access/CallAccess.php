<?php

namespace src\access;

use common\models\UserProjectParams;
use src\auth\Auth;
use Yii;
use yii\caching\TagDependency;

class CallAccess
{
    public const TAG = 'user_can_dial_tag_';

    public static function isUserCanDial(int $userId, int $callType): bool
    {
        if (!Auth::can('PhoneWidget')) {
            return false;
        }

        return Yii::$app->cache->getOrSet('user-' . $userId, static function () use ($userId, $callType) {
            return UserProjectParams::find()
                ->byUserId($userId)
                ->withExistingPhoneInPhoneList()
                ->withCallTypeParams($callType)
                ->exists();
        }, 0, new TagDependency(['tags' => self::TAG . $userId]));
    }

    public static function flush(int $userId): void
    {
        TagDependency::invalidate(Yii::$app->cache, self::TAG . $userId);
    }
}
