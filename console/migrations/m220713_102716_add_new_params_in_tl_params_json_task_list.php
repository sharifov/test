<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220713_102716_add_new_params_in_tl_params_json_task_list
 */
class m220713_102716_add_new_params_in_tl_params_json_task_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $taskLists = (new Query())
            ->from('{{%task_list}}')->all();

        try {
            foreach ($taskLists as $taskList) {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                $params['duration'] = 0;

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $taskLists = (new Query())
            ->from('{{%task_list}}')->all();

        try {
            foreach ($taskLists as $taskList) {
                $params = JsonHelper::decode($taskList['tl_params_json'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($params['duration'])) {
                    unset($params['duration']);
                }

                (new Query())->createCommand()->update('{{%task_list}}', [
                    'tl_params_json' => $params
                ], ['tl_id' => (int)$taskList['tl_id']])->execute();
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}
