<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200214_081615_create_permissions_task_close
 */
class m200214_081615_create_permissions_task_close extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $close = $auth->createPermission('qa-task/qa-task-action/close');
        $close->description = 'Task Close';
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

        if ($permission = $auth->getPermission('qa-task/qa-task-action/close')) {
            $auth->remove($permission);
        }
    }
}
