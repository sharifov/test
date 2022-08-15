<?php

use frontend\helpers\JsonHelper;
use modules\taskList\src\entities\TaskObject;
use modules\taskList\src\objects\TargetObjectList;
use modules\taskList\src\services\TaskListService;
use src\helpers\app\AppHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220815_085841_add_sms_params_json_task_list
 */
class m220815_085841_add_sms_params_json_task_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $taskLists = (new Query())
            ->from('{{%task_list}}')
            ->select(['tl_id', 'tl_params_json'])
            ->where(['tl_object' => TaskObject::OBJ_SMS])
            ->all();

        foreach ($taskLists as $taskList) {
            try {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                if (!empty($params[TaskListService::PARAM_KEY_SMS_EXCLUDE_PROJECTS])) {
                    continue;
                }
                $params[TaskListService::PARAM_KEY_SMS_EXCLUDE_PROJECTS] = 'priceline,kayak';

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            } catch (Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['tl_id'] = $taskList['tl_id'] ?? null;
                \Yii::error($message, 'm220815_085841_add_sms_params_json_task_list:safeUp');
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
            ->where(['tl_object' => TaskObject::OBJ_SMS])
            ->all();

        foreach ($taskLists as $taskList) {
            try {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($params[TaskListService::PARAM_KEY_SMS_EXCLUDE_PROJECTS])) {
                    unset($params[TaskListService::PARAM_KEY_SMS_EXCLUDE_PROJECTS]);
                }

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            } catch (Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['tl_id'] = $taskList['tl_id'] ?? null;
                \Yii::error($message, 'm220815_085841_add_sms_params_json_task_list:safeDown');
                echo $throwable->getMessage();
            }
        }
    }
}
