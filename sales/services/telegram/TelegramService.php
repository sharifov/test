<?php

namespace sales\services\telegram;

use common\models\UserProfile;
use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use Yii;

/**
 * Class TelegramService
 */
class TelegramService
{
    public const DESCRIPTION_BOT_BLOCKED_BY_USER = 'Forbidden: bot was blocked by the user';
    public const DESCRIPTION_USER_NOT_SUBSCRIBED_ON_BOT = 'Bad Request: chat not found';

    /**
     * @param string $responseBody
     * @return array|null
     */
    public static function getResponseFromStringBody(string $responseBody): ?array
    {
        try {
            if (($beginPos = strpos($responseBody, '{')) !== false && ($endPos = strpos($responseBody, '}')) !== false) {
                $rawJson = substr($responseBody, $beginPos, ($endPos - $beginPos) + 1);
                $rawJson = stripslashes($rawJson);
                return JsonHelper::decode($rawJson);
            }
        } catch (\Throwable $throwable) {
            Yii::warning(
                AppHelper::throwableFormatter($throwable),
                'TelegramService:getResponseFromStringBody'
            );
        }
        return null;
    }

    /**
     * @param string $responseBody
     * @return bool
     */
    public static function isBotBlockedByUser(string $responseBody): bool
    {
        $response = self::getResponseFromStringBody($responseBody);
        return !empty($response['description']) && $response['description'] === self::DESCRIPTION_BOT_BLOCKED_BY_USER;
    }

    /**
     * @param string $responseBody
     * @return bool
     */
    public static function isUserSubscribedOnBot(string $responseBody): bool
    {
        $response = self::getResponseFromStringBody($responseBody);
        return !empty($response['description']) && $response['description'] === self::DESCRIPTION_USER_NOT_SUBSCRIBED_ON_BOT;
    }

    /**
     * @param int $userId
     * @return null|string
     */
    public static function getTelegramChatIdByUserId(?int $userId): ?string
    {
        $profile = UserProfile::findOne(['up_user_id' => (int) $userId]);
        if ($profile && $profile->up_telegram && $profile->up_telegram_enable) {
            return $profile->up_telegram;
        }
        return null;
    }
}
