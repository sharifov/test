<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200325_094016_add_permissions_call_logs
 */
class m200325_094016_add_permissions_call_logs extends Migration
{
    public $route = [
        '/call-log/*',
        '/call-log-case/*',
        '/call-log-lead/*',
        '/call-log-queue/*',
        '/call-log-record/*',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
