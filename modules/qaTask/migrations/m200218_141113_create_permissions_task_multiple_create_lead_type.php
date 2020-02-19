<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200218_141113_create_permissions_task_multiple_create_lead_type
 */
class m200218_141113_create_permissions_task_multiple_create_lead_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $close = $auth->createPermission('leads/index_Create_QA_Tasks');
        $close->description = 'Lead search page. Multiple create tasks.';
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

        if ($permission = $auth->getPermission('leads/index_Create_QA_Tasks')) {
            $auth->remove($permission);
        }
    }
}
