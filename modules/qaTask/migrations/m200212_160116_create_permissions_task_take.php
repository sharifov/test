<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\take\QaTaskTakeEscalateRule;
use modules\qaTask\src\rbac\rules\task\actions\take\QaTaskTakePendingRule;
use Yii;
use yii\db\Migration;

/**
 * Class m200212_160116_create_permissions_task_take
 */
class m200212_160116_create_permissions_task_take extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $take = $auth->createPermission('qa-task/task/take');
        $take->description = 'Task Take';
        $auth->add($take);

        $takePendingRule = new QaTaskTakePendingRule();
        $auth->add($takePendingRule);
        $takePending = $auth->createPermission('qa-task/task/take_Pending');
        $takePending->description = 'Task Take only from status Pending';
        $takePending->ruleName = $takePendingRule->name;
        $auth->add($takePending);
        $auth->addChild($takePending, $take);

        $takeEscalateRule = new QaTaskTakeEscalateRule();
        $auth->add($takeEscalateRule);
        $takeEscalate = $auth->createPermission('qa-task/task/take_Escalate');
        $takeEscalate->description = 'Task Take only from status Escalate';
        $takeEscalate->ruleName = $takeEscalateRule->name;
        $auth->add($takeEscalate);
        $auth->addChild($takeEscalate, $take);

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

        if ($permission = $auth->getPermission('qa-task/task/take')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/take_Pending_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/task/take_Pending')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/take_Escalate_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/task/take_Escalate')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
