<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\rbac\rules\task\actions\takeOver\QaTaskTakeOverEscalateRule;
use modules\qaTask\src\rbac\rules\task\actions\takeOver\QaTaskTakeOverProcessingRule;
use Yii;
use yii\db\Migration;

/**
 * Class m200213_125049_create_permissions_task_take_over
 */
class m200213_125049_create_permissions_task_take_over extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $takeOver = $auth->createPermission('qa-task/task/take-over');
        $takeOver->description = 'Task Take Over';
        $auth->add($takeOver);

        $takeOverProcessingRule = new QaTaskTakeOverProcessingRule();
        $auth->add($takeOverProcessingRule);
        $takeOverProcessing = $auth->createPermission('qa-task/task/take-over_Processing');
        $takeOverProcessing->description = 'Task Take Over only from status Processing';
        $takeOverProcessing->ruleName = $takeOverProcessingRule->name;
        $auth->add($takeOverProcessing);
        $auth->addChild($takeOverProcessing, $takeOver);

        $takeOverEscalateRule = new QaTaskTakeOverEscalateRule();
        $auth->add($takeOverEscalateRule);
        $takeOverEscalate = $auth->createPermission('qa-task/task/take-over_Escalate');
        $takeOverEscalate->description = 'Task Take Over only from status Escalate';
        $takeOverEscalate->ruleName = $takeOverEscalateRule->name;
        $auth->add($takeOverEscalate);
        $auth->addChild($takeOverEscalate, $takeOver);

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

        if ($permission = $auth->getPermission('qa-task/task/take-over')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/take-over_Processing_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/task/take-over_Processing')) {
            $auth->remove($permission);
        }
        if ($rule = $auth->getRule('qa-task/task/take-over_Escalate_Rule')) {
            $auth->remove($rule);
        }
        if ($permission = $auth->getPermission('qa-task/task/take-over_Escalate')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
