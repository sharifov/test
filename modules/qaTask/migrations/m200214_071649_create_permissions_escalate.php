<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\escalate\QaTaskEscalateRule;
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

        $escalateRule = new QaTaskEscalateRule();
        $auth->add($escalateRule);
        $escalate = $auth->createPermission('qa-task/task/escalate');
        $escalate->description = 'Task Escalate';
        $escalate->ruleName = $escalateRule->name;
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

        if ($permission = $auth->getPermission('qa-task/task/escalate')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/escalate_Rule')) {
            $auth->remove($rule);
        }
    }
}
