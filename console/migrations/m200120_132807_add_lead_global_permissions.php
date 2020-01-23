<?php

use common\models\Employee;
use sales\rbac\rules\globalRules\lead\IsEmptyOwnerRule;
use sales\rbac\rules\globalRules\lead\IsOwnerMyGroupRule;
use sales\rbac\rules\globalRules\lead\IsOwnerRule;
use yii\db\Migration;

/**
 * Class m200120_132807_add_lead_global_permissions
 */
class m200120_132807_add_lead_global_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        /** */

        $isOwnerRule = new IsOwnerRule();
        $auth->add($isOwnerRule);

        $isOwner = $auth->createPermission('global/lead/isOwner');
        $isOwner->description = 'Global Lead Is Owner';
        $isOwner->ruleName = $isOwnerRule->name;
        $auth->add($isOwner);

        /** */

        $isEmptyOwnerRule = new IsEmptyOwnerRule();
        $auth->add($isEmptyOwnerRule);

        $isEmptyOwner = $auth->createPermission('global/lead/isEmptyOwner');
        $isEmptyOwner->description = 'Global Lead Is Empty Owner';
        $isEmptyOwner->ruleName = $isEmptyOwnerRule->name;
        $auth->add($isEmptyOwner);

        /** */

        $isOwnerMyGroupRule = new IsOwnerMyGroupRule();
        $auth->add($isOwnerMyGroupRule);

        $isOwnerMyGroup = $auth->createPermission('global/lead/isOwnerMyGroup');
        $isOwnerMyGroup->description = 'Global Lead Is Owner My Group';
        $isOwnerMyGroup->ruleName = $isOwnerMyGroupRule->name;
        $auth->add($isOwnerMyGroup);

        /** */

        if ($agent = $auth->getRole(Employee::ROLE_AGENT)) {
            $auth->addChild($agent, $isOwner);
        }

        if ($supervision = $auth->getRole(Employee::ROLE_SUPERVISION)) {
            $auth->addChild($supervision, $isOwner);
            $auth->addChild($supervision, $isOwnerMyGroup);
            $auth->addChild($supervision, $isEmptyOwner);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('global/lead/isOwnerMyGroup')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('global/lead/isOwnerMyGroupRule')) {
            $auth->remove($rule);
        }

        if ($permission = $auth->getPermission('global/lead/isEmptyOwner')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('global/lead/isEmptyOwnerRule')) {
            $auth->remove($rule);
        }

        if ($permission = $auth->getPermission('global/lead/isOwner')) {
            $auth->remove($permission);
        }

        if ($rule = $auth->getRule('global/lead/isOwnerRule')) {
            $auth->remove($rule);
        }
    }
}
