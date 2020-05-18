<?php

use yii\db\Migration;

/**
 * Class m190628_053616_add_permissions_to_lead_flow_checklist_controller
 */
class m190628_053616_add_permissions_to_lead_flow_checklist_controller extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;

        $viewLeadFlowChecklist = $auth->createPermission('viewLeadFlowChecklist');
        $auth->add($viewLeadFlowChecklist);

        $supervision = $auth->getRole('supervision');
        $admin = $auth->getRole('admin');

        $auth->addChild($supervision, $viewLeadFlowChecklist);
        $auth->addChild($admin, $viewLeadFlowChecklist);

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

        if ($viewLeadFlowChecklist = $auth->getPermission('viewLeadFlowChecklist')) {
            $auth->remove($viewLeadFlowChecklist);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
