<?php

namespace src\services\telegram;

use common\models\UserProfile;
use frontend\helpers\JsonHelper;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class TelegramService
 *
 * @link https://core.telegram.org/api/errors#403-forbidden
 */
class TelegramService
{
    public const TYPE_BOT_BLOCKED_BY_USER = 'Forbidden: bot was blocked by the user';
    public const TYPE_USER_NOT_SUBSCRIBED_ON_BOT = 'Bad Request: chat not found';
    public const TYPE_USER_DEACTIVATED = 'Forbidden: user is deactivated';

    public const TYPE_LIST = [
        self::TYPE_BOT_BLOCKED_BY_USER,
        self::TYPE_USER_NOT_SUBSCRIBED_ON_BOT,
        self::TYPE_USER_DEACTIVATED,
    ];

    public const NOTIFICATION_MAP = [
        self::TYPE_BOT_BLOCKED_BY_USER => [
            'title' => 'Telegram was disabled',
            'message' => 'Telegram was disabled due to blocking on the client side',
        ],
        self::TYPE_USER_NOT_SUBSCRIBED_ON_BOT => [
            'title' => 'Telegram bot was changed',
            'message' => 'Please try to subscribe to new Telegram Bot',
        ],
        self::TYPE_USER_DEACTIVATED => [
            'title' => 'Telegram was disabled',
            'message' => 'Telegram was disabled due to user is deactivated',
        ],
    ];

    public const FILTER_SYMBOL_LIST = [
        '_', '@', '*', '`',
    ];

    public const LAST_MESSAGE_TO_USER = 'lastMessageToUser';

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
     * @return string|null
     */
    public static function detectDisableType(string $responseBody): ?string
    {
        $response = self::getResponseFromStringBody($responseBody);

        if (!empty($response['description']) && ArrayHelper::isIn($response['description'], self::TYPE_LIST)) {
            return $response['description'];
        }
        return null;
    }

    public static function generateNotificationData(string $type): ?array
    {
        if (ArrayHelper::keyExists($type, self::NOTIFICATION_MAP)) {
            return self::NOTIFICATION_MAP[$type];
        }
        return null;
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

    public static function cleanText(string $text): string
    {
        return str_replace(self::FILTER_SYMBOL_LIST, '', strip_tags($text));
    }

    public static function prepareText(string $text): string
    {
        $env = strtoupper(YII_ENV);
        $message = self::cleanText($text) . PHP_EOL;
        if ($env !== 'PROD') {
            $message .= ' Environment: ' . $env;
        }
        return $message;
    }

    public static function setLastTimeMessageToUser(int $userId): void
    {
        Yii::$app->redis->set(self::getRedisKeyForLastMessageToUser($userId), time());
    }

    public static function getTimeForLastSentMessageToUser(int $userId): int
    {
        $timestamp = Yii::$app->redis->get(self::getRedisKeyForLastMessageToUser($userId)) ?? 0;

        return intval($timestamp);
    }

    private static function getRedisKeyForLastMessageToUser(int $userId): string
    {
        return self::LAST_MESSAGE_TO_USER . $userId;
    }

    public static function delayForTelegramMessagesIsEnable(): bool
    {
        /** @fflag FFlag::FF_KEY_TELEGRAM_MESSAGE_DELAY_ENABLE, Delay for telegram notification messages */
        return Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_TELEGRAM_MESSAGE_DELAY_ENABLE);
    }
}
