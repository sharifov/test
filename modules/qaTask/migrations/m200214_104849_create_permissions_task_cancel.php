<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\cancel\QaTaskCancelEscalateRule;
use modules\qaTask\src\rbac\rules\task\actions\cancel\QaTaskCancelPendingRule;
use modules\qaTask\src\rbac\rules\task\actions\cancel\QaTaskCancelProcessingCurrentRule;
use modules\qaTask\src\rbac\rules\task\actions\cancel\QaTaskCancelProcessingRule;
use Yii;
use yii\db\Migration;

/**
 * Class m200214_104849_create_permissions_task_cancel
 */
class m200214_104849_create_permissions_task_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $cancel = $auth->createPermission('qa-task/qa-task-action/cancel');
        $cancel->description = 'Task Cancel';
        $auth->add($cancel);

        $cancelProcessingCurrentRule = new QaTaskCancelProcessingCurrentRule();
        $auth->add($cancelProcessingCurrentRule);
        $cancelProcessingCurrent = $auth->createPermission('qa-task/qa-task-action/cancel_Processing_Current');
        $cancelProcessingCurrent->description = 'Task Cancel only from status Processing and Current user is assigned';
        $cancelProcessingCurrent->ruleName = $cancelProcessingCurrentRule->name;
        $auth->add($cancelProcessingCurrent);
        $auth->addChild($cancelProcessingCurrent, $cancel);

        $cancelProcessingRule = new QaTaskCancelProcessingRule();
        $auth->add($cancelProcessingRule);
        $cancelProcessing = $auth->createPermission('qa-task/qa-task-action/cancel_Processing');
        $cancelProcessing->description = 'Task Cancel only from status Processing';
        $cancelProcessing->ruleName = $cancelProcessingRule->name;
        $auth->add($cancelProcessing);
        $auth->addChild($cancelProcessing, $cancel);

        $cancelPendingRule = new QaTaskCancelPendingRule();
        $auth->add($cancelPendingRule);
        $cancelPending = $auth->createPermission('qa-task/qa-task-action/cancel_Pending');
        $cancelPending->description = 'Task Cancel only from status Pending';
        $cancelPending->ruleName = $cancelPendingRule->name;
        $auth->add($cancelPending);
        $auth->addChild($cancelPending, $cancel);

        $cancelEscalateRule = new QaTaskCancelEscalateRule();
        $auth->add($cancelEscalateRule);
        $cancelEscalate = $auth->createPermission('qa-task/qa-task-action/cancel_Escalate');
        $cancelEscalate->description = 'Task Cancel only from status Escalated and Current user is assigned';
        $cancelEscalate->ruleName = $cancelEscalateRule->name;
        $auth->add($cancelEscalate);
        $auth->addChild($cancelEscalate, $cancel);

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

        if ($permission = $auth->getPermission('qa-task/qa-task-action/cancel')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/cancel_Processing_Current_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/cancel_Processing_Current')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/cancel_Processing_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/cancel_Processing')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/cancel_Pending_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/cancel_Pending')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/cancel_Escalate_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/cancel_Escalate')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
