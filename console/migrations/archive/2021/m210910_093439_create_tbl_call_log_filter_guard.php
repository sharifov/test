<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210910_093439_create_tbl_call_log_filter_guard
 */
class m210910_093439_create_tbl_call_log_filter_guard extends Migration
{
    private $routes = [
        '/call-log-filter-guard-crud/index',
        '/call-log-filter-guard-crud/create',
        '/call-log-filter-guard-crud/view',
        '/call-log-filter-guard-crud/update',
        '/call-log-filter-guard-crud/delete',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%call_log_filter_guard}}', [
            'clfg_call_id' => $this->primaryKey(),
            'clfg_type' => $this->tinyInteger(),
            'clfg_sd_rate' => $this->float(),
            'clfg_trust_percent' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('IND-call_log_filter_guard-clfg_type', '{{%call_log_filter_guard}}', 'clfg_type');

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_log_filter_guard-clfg_type', '{{%call_log_filter_guard}}');
        $this->dropTable('{{%call_log_filter_guard}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
