<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\close\QaTaskCloseRule;
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

        $closeRule = new QaTaskCloseRule();
        $auth->add($closeRule);

        $close = $auth->createPermission('qa-task/task/close');
        $close->description = 'Task Close';
        $close->ruleName = $closeRule->name;
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

        if ($permission = $auth->getPermission('qa-task/task/close')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/close_Rule')) {
            $auth->remove($rule);
        }
    }
}
