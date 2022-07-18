<?php

namespace common\components\jobs;

use common\models\UserProfile;
use src\helpers\app\AppHelper;
use src\services\telegram\TelegramService;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use Yii;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use common\models\Notifications;

/**
 * This is the model class for "TelegramSendMessage".
 *
 * @property int $user_id
 * @property string $text
 */
class TelegramSendMessageJob extends BaseJob implements RetryableJobInterface
{
    public $user_id;
    public $text;

    public const DELAY_SECONDS = 2;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();

        if (TelegramService::delayForTelegramMessagesIsEnable() === true) {
            $lastMessageToUser = TelegramService::getTimeForLastSentMessageToUser($this->user_id);

            if ($lastMessageToUser >= time()) {
                $job = new TelegramSendMessageJob();
                $job->user_id = $this->user_id;
                $job->text = $this->text;
                $job->delayJob = self::DELAY_SECONDS;

                Yii::$app->queue_job->delay(self::DELAY_SECONDS)->priority(100)->push($job);

                return false;
            }
        }

        try {
            if (!is_string($this->text)) {
                throw new \RuntimeException('Text is not string');
            }

            if ($this->user_id && $telegramChatId = TelegramService::getTelegramChatIdByUserId($this->user_id)) {
                $tgm = Yii::$app->telegram;
                $tgm->sendMessage([
                    'chat_id' => $telegramChatId,
                    'text' => TelegramService::prepareText($this->text),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => false
                ]);

                if (TelegramService::delayForTelegramMessagesIsEnable() === true) {
                    TelegramService::setLastTimeMessageToUser($this->user_id);
                }

                unset($tgm);
                return true;
            }
        } catch (\Throwable $throwable) {
            $errorMessage = VarDumper::dumpAsString($throwable->getMessage());

            if ($disableType = TelegramService::detectDisableType($errorMessage)) {
                Yii::info(VarDumper::dumpAsString([
                    'message' => $errorMessage,
                    'userId' => $this->user_id,
                ]), 'info\TelegramJob:execute');

                UserProfile::disableTelegramByUserId((int) $this->user_id);

                if ($notificationData = TelegramService::generateNotificationData($disableType)) {
                    Notifications::createAndPublish(
                        $this->user_id,
                        $notificationData['title'],
                        $notificationData['message'],
                        Notifications::TYPE_INFO
                    );
                }
            } else {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                    'userId' => $this->user_id,
                    'text' => $this->text,
                    'textPrepared' => TelegramService::prepareText($this->text),
                    'attempts' => $queue->attempts,
                ]);

                if ($queue->attempts > 1) {
                    \Yii::warning($message, 'TelegramJob:execute:catch');
                } else {
                    \Yii::error($message, 'TelegramJob:execute:catch');
                }
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
