<?php

use yii\db\Migration;

/**
 * Class m190805_075934_create_permissions_for_lead_search
 */
class m190805_075934_create_permissions_for_lead_search extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');
        $supervision = $auth->getRole('supervision');

        $leadSearchMultipleSelectPermission = $auth->createPermission('leadSearchMultipleSelect');
        $leadSearchMultipleSelectPermission->description = 'Lead Search Multiple Select';
        $auth->add($leadSearchMultipleSelectPermission);
        $auth->addChild($admin, $leadSearchMultipleSelectPermission);
        $auth->addChild($supervision, $leadSearchMultipleSelectPermission);

        $leadSearchMultipleSelectAllPermission = $auth->createPermission('leadSearchMultipleSelectAll');
        $leadSearchMultipleSelectAllPermission->description = 'Lead Search Multiple Select All';
        $auth->add($leadSearchMultipleSelectAllPermission);
        $auth->addChild($admin, $leadSearchMultipleSelectAllPermission);

        $leadSearchMultipleUpdatePermission = $auth->createPermission('leadSearchMultipleUpdate');
        $leadSearchMultipleUpdatePermission->description = 'Lead Search Multiple Update';
        $auth->add($leadSearchMultipleUpdatePermission);
        $auth->addChild($admin, $leadSearchMultipleUpdatePermission);

        $leadSearchMultipleUpdateSupervisionRule = new \sales\rbac\rules\LeadSearchMultipleUpdateSupervisionRule();
        $auth->add($leadSearchMultipleUpdateSupervisionRule);
        $leadSearchMultipleUpdateSupervisionPermission = $auth->createPermission('leadSearchMultipleUpdateSupervisionPermission');
        $leadSearchMultipleUpdateSupervisionPermission->description = 'Lead Search Multiple Update for Supervision';
        $leadSearchMultipleUpdateSupervisionPermission->ruleName = $leadSearchMultipleUpdateSupervisionRule->name;
        $auth->add($leadSearchMultipleUpdateSupervisionPermission);
        $auth->addChild($leadSearchMultipleUpdateSupervisionPermission, $leadSearchMultipleUpdatePermission);
        $auth->addChild($supervision, $leadSearchMultipleUpdateSupervisionPermission);

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

        $leadSearchMultipleSelectSupervisionPermission = $auth->getPermission('leadSearchMultipleUpdateSupervisionPermission');
        $auth->remove($leadSearchMultipleSelectSupervisionPermission);

        $leadSearchMultipleSelectSupervisionRule = $auth->getRule('LeadSearchMultipleUpdateSupervisionRule');
        $auth->remove($leadSearchMultipleSelectSupervisionRule);

        $leadSearchMultipleSelectPermission = $auth->getPermission('leadSearchMultipleUpdate');
        $auth->remove($leadSearchMultipleSelectPermission);

        $leadSearchMultipleSelectPermission = $auth->getPermission('leadSearchMultipleSelectAll');
        $auth->remove($leadSearchMultipleSelectPermission);

        $leadSearchMultipleSelectPermission = $auth->getPermission('leadSearchMultipleSelect');
        $auth->remove($leadSearchMultipleSelectPermission);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
