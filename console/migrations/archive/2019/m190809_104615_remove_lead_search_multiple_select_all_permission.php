<?php

use yii\db\Migration;

/**
 * Class m190809_104615_remove_lead_search_multiple_select_all_permission
 */
class m190809_104615_remove_lead_search_multiple_select_all_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadSearchMultipleSelectPermission = $auth->getPermission('leadSearchMultipleSelectAll');
        $auth->remove($leadSearchMultipleSelectPermission);

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
        $admin = $auth->getRole('admin');

        $leadSearchMultipleSelectAllPermission = $auth->createPermission('leadSearchMultipleSelectAll');
        $leadSearchMultipleSelectAllPermission->description = 'Lead Search Multiple Select All';
        $auth->add($leadSearchMultipleSelectAllPermission);
        $auth->addChild($admin, $leadSearchMultipleSelectAllPermission);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
