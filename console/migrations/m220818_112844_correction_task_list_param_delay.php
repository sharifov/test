<?php

use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220818_112844_correction_task_list_param_delay
 */
class m220818_112844_correction_task_list_param_delay extends Migration
{
    private const PARAM_DELAY_SHIFT = 'delayShift';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $taskLists = (new Query())
            ->from('{{%task_list}}')
            ->select(['tl_id', 'tl_params_json'])
            ->all();

        foreach ($taskLists as $taskList) {
            try {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                $params[self::PARAM_DELAY_SHIFT] = 0;

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            } catch (Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['tl_id'] = $taskList['tl_id'] ?? null;
                \Yii::error($message, 'm220818_112844_correction_task_list_param_delay:safeUp');
                echo $throwable->getMessage();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $taskLists = (new Query())
            ->from('{{%task_list}}')
            ->select(['tl_id', 'tl_params_json'])
            ->all();

        foreach ($taskLists as $taskList) {
            try {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($params[self::PARAM_DELAY_SHIFT])) {
                    unset($params[self::PARAM_DELAY_SHIFT]);
                }

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            } catch (Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['tl_id'] = $taskList['tl_id'] ?? null;
                \Yii::error($message, 'm220818_112844_correction_task_list_param_delay:safeDown');
                echo $throwable->getMessage();
            }
        }
    }
}
