<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\returnTask\QaTaskReturnEscalateRule;
use modules\qaTask\src\rbac\rules\task\actions\returnTask\QaTaskReturnProcessingCurrentRule;
use modules\qaTask\src\rbac\rules\task\actions\returnTask\QaTaskReturnProcessingRule;
use Yii;
use yii\db\Migration;

/**
 * Class m200214_142132_create_permissions_task_return
 */
class m200214_142132_create_permissions_task_return extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $return = $auth->createPermission('qa-task/qa-task-action/return');
        $return->description = 'Task Return';
        $auth->add($return);

        $returnToEscalate = $auth->createPermission('qa-task/qa-task-action/return_To_Escalate');
        $returnToEscalate->description = 'Task Return to Escalate';
        $auth->add($returnToEscalate);

        $returnProcessingCurrentRule = new QaTaskReturnProcessingCurrentRule();
        $auth->add($returnProcessingCurrentRule);
        $returnProcessingCurrent = $auth->createPermission('qa-task/qa-task-action/return_Processing_Current');
        $returnProcessingCurrent->description = 'Task Return only from status Processing and Current user is assigned';
        $returnProcessingCurrent->ruleName = $returnProcessingCurrentRule->name;
        $auth->add($returnProcessingCurrent);
        $auth->addChild($returnProcessingCurrent, $return);

        $returnProcessingRule = new QaTaskReturnProcessingRule();
        $auth->add($returnProcessingRule);
        $returnProcessing = $auth->createPermission('qa-task/qa-task-action/return_Processing');
        $returnProcessing->description = 'Task Return only from status Processing';
        $returnProcessing->ruleName = $returnProcessingRule->name;
        $auth->add($returnProcessing);
        $auth->addChild($returnProcessing, $return);

        $returnEscalateRule = new QaTaskReturnEscalateRule();
        $auth->add($returnEscalateRule);
        $returnEscalate = $auth->createPermission('qa-task/qa-task-action/return_Escalate');
        $returnEscalate->description = 'Task Return only from status Escalated and Current user is assigned';
        $returnEscalate->ruleName = $returnEscalateRule->name;
        $auth->add($returnEscalate);
        $auth->addChild($returnEscalate, $return);

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

        if ($permission = $auth->getPermission('qa-task/qa-task-action/return')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('qa-task/qa-task-action/return_To_Escalate')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/return_Processing_Current_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/return_Processing_Current')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/return_Processing_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/return_Processing')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('qa-task/qa-task-action/return_Escalate_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/qa-task-action/return_Escalate')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
