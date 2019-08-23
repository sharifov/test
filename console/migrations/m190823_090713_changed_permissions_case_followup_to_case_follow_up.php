<?php

use yii\db\Migration;

/**
 * Class m190823_090713_changed_permissions_case_followup_to_case_follow_up
 */
class m190823_090713_changed_permissions_case_followup_to_case_follow_up extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission('/cases-q/followup');
        $auth->remove($permission);

        $permission = $auth->createPermission('/cases-q/follow-up');
        $auth->add($permission);

        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $permission);
        }

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

        $permission = $auth->getPermission('/cases-q/follow-up');
        $auth->remove($permission);

        $permission = $auth->createPermission('/cases-q/followup');
        $auth->add($permission);

        foreach (['admin', 'sup_agent', 'sup_super', 'ex_agent', 'ex_super'] as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }


}
