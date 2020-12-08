<?php

namespace sales\model\clientChatHold\service;

use frontend\helpers\JsonHelper;
use sales\model\clientChatHold\entity\ClientChatHold;
use Yii;

/**
 * Class ClientChatHoldService
 *
 * @property array $deadlineOptions
 */
class ClientChatHoldService
{
    public $deadlineOptions;

    public function __construct()
    {
        $this->deadlineOptions = JsonHelper::decode(Yii::$app->params['settings']['client_chat_hold_deadline_options']);
    }

    public function formattedDeadlineOptions(): array
    {
        $formatted = [];
        foreach ($this->deadlineOptions as $key => $value) {
            $seconds = $value * 60;
            $formatted[$key] = self::formatTimeFromSeconds($seconds);
        }
        return $formatted;
    }

    public static function formatTimeFromSeconds(int $seconds): string
    {
        $dtStart = new \DateTime('@0');
        $dtEnd = new \DateTime("@$seconds");
        $diff = $dtStart->diff($dtEnd);

        $minString = '1 minute';
        if ($diff->i !== 1) {
            $minString = $diff->format('%I') . ' minutes';
        }

        $hoursString = '';
        if ($diff->h) {
            $hoursString = '1 hour ';
            if ($diff->h !== 1) {
                $hoursString = $diff->format('%H') . ' hours ';
            }
        }
        return $hoursString . $minString;
    }

    public static function isMoreThanHourLeft(ClientChatHold $clientChatHold): bool
    {
        $seconds = $clientChatHold->deadlineNowDiffInSeconds();
        $dtStart = new \DateTime('@0');
        $dtEnd = new \DateTime("@$seconds");
        $diff = $dtStart->diff($dtEnd);
        return $diff->h > 0;
    }
}
