<?php

/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-04-22
 */

namespace common\components\jobs;

use common\models\UserProfile;
use sales\services\telegram\TelegramService;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * This is the model class for "TelegramSendMessage".
 *
 * @property int $user_id
 * @property string $text
 */

class TelegramSendMessageJob implements RetryableJobInterface
{
    public $user_id;
    public $text;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        try {
            if ($this->user_id && $telegramChatId = TelegramService::getTelegramChatIdByUserId($this->user_id)) {
                $tgm = Yii::$app->telegram;
                $tgm->sendMessage([
                    'chat_id' => $telegramChatId,
                    'text' => strip_tags($this->text),
                ]);

                unset($tgm);
                return true;
            }
        } catch (\Throwable $throwable) {
            $errorMessage = VarDumper::dumpAsString($throwable->getMessage());

            if (TelegramService::isBotBlockedByUser($errorMessage)) {
                Yii::info(VarDumper::dumpAsString([
                    'message' => $errorMessage,
                    'userId' => $this->user_id,
                ]), 'info\TelegramJob:execute:catch');

                UserProfile::disableTelegramByUserId((int) $this->user_id);
            } else {
                Yii::error(VarDumper::dumpAsString([
                    'message' => $errorMessage,
                    'userId' => $this->user_id,
                ]), 'TelegramJob:execute:catch');
            }
        }
        return false;
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 30;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }
}
