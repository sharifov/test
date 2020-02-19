<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200218_121846_create_permissions_task_view_tasks_obj_lead
 */
class m200218_121846_create_permissions_task_view_tasks_obj_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $close = $auth->createPermission('lead/view_QA_Tasks');
        $close->description = 'Lead view page. QA tasks menu.';
        $auth->add($close);

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

        if ($permission = $auth->getPermission('lead/view_QA_Tasks')) {
            $auth->remove($permission);
        }
    }
}
