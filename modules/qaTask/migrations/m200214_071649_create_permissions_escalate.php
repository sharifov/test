<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200214_071649_create_permissions_escalate
 */
class m200214_071649_create_permissions_escalate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $escalate = $auth->createPermission('qa-task/qa-task-action/escalate');
        $escalate->description = 'Task Escalate';
        $auth->add($escalate);

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

        if ($permission = $auth->getPermission('qa-task/qa-task-action/escalate')) {
            $auth->remove($permission);
        }
    }
}
