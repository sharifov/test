<?php

use yii\db\Migration;

/**
 * Class m190613_121815_add_access_for_admin_api_report_page
 */
class m190613_121815_add_access_for_admin_api_report_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = \Yii::$app->authManager;

        $adminRole = $auth->getRole('admin');
        $permission = $auth->createPermission('/stats/api-graph');
        $auth->add($permission);
        $auth->addChild($adminRole, $permission);

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

        $permission = $auth->getPermission('/stats/api-graph');
        if($permission) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
