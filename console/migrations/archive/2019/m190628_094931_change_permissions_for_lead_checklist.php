<?php

use yii\db\Migration;

/**
 * Class m190628_094931_change_permissions_for_lead_checklist
 */
class m190628_094931_change_permissions_for_lead_checklist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('/lead-checklist/*')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('/lead-checklist-type/*')) {
            $auth->remove($permission);
        }

        $admin = $auth->getRole('admin');

        $manageLeadChecklist = $auth->createPermission('manageLeadChecklist');
        $auth->add($manageLeadChecklist);
        $auth->addChild($admin, $manageLeadChecklist);

        $manageLeadChecklistType = $auth->createPermission('manageLeadChecklistType');
        $auth->add($manageLeadChecklistType);
        $auth->addChild($admin, $manageLeadChecklistType);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = \Yii::$app->authManager;

        if ($permission = $auth->getPermission('manageLeadChecklist')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('manageLeadChecklistType')) {
            $auth->remove($permission);
        }

        $admin = $auth->getRole('admin');

        $permission = $auth->createPermission('/lead-checklist-type/*');
        $auth->add($permission);
        $auth->addChild($admin, $permission);

        $permission = $auth->createPermission('/lead-checklist/*');
        $auth->add($permission);
        $auth->addChild($admin, $permission);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
