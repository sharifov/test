<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200215_111842_create_permissions_task_decide
 */
class m200215_111842_create_permissions_task_decide extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $close = $auth->createPermission('qa-task/qa-task-action/decide');
        $close->description = 'Task Decide';
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

        if ($permission = $auth->getPermission('qa-task/qa-task-action/decide')) {
            $auth->remove($permission);
        }
    }
}
