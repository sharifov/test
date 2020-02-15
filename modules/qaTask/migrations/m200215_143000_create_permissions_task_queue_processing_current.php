<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200215_143000_create_permissions_task_queue_processing_current
 */
class m200215_143000_create_permissions_task_queue_processing_current extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $close = $auth->createPermission('qa-task/qa-task-queue/processing_Current');
        $close->description = 'Task Queue Processing Current';
        $auth->add($close);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('qa-task/qa-task-queue/processing_Current')) {
            $auth->remove($permission);
        }
    }
}
