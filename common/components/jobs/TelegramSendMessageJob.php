<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-04-22
 */

namespace common\components\jobs;

use common\models\UserProfile;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the model class for "TelegramSendMessage".
 *
 * @property int $user_id
 * @property string $text
 */

class TelegramSendMessageJob extends BaseObject implements JobInterface
{
    public $user_id;
    public $text;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {

        try {

            if($this->user_id) {
                $profile = UserProfile::find()->where(['up_user_id' => $this->user_id])->limit(1)->one();

                Yii::info($this->user_id, 'info\TelegramJob:execute:info');

                if ($profile && $profile->up_telegram && $profile->up_telegram_enable) {

                    $tgm = Yii::$app->telegram;

                    $tgm->sendMessage([
                        'chat_id' => $profile->up_telegram,
                        'text' => $this->text,
                    ]);

                    unset($tgm, $profile);
                    return true;
                }
            }



        } catch (\Throwable $e) {

            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'TelegramJob:execute:catch');
        }

        return false;
    }

    /*public function getTtr()
    {
        return 1 * 60;
    }*/

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}