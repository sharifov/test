<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220427_113117_create_tbl_schedule_request
 */
class m220427_113117_create_tbl_shift_schedule_request extends Migration
{
    public array $routes = [
        '/shift/shift-schedule-request/*',  // user
    ];

    public array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shift_schedule_request}}', [
            'ssr_id' => $this->primaryKey(),
            'ssr_uss_id' => $this->integer(),
            'ssr_sst_id' => $this->integer(),
            'ssr_status_id' => $this->integer()->notNull(),
            'ssr_description' => $this->string(1000),
            'ssr_created_dt' => $this->dateTime(),
            'ssr_updated_dt' => $this->dateTime(),
            'ssr_created_user_id' => $this->integer(),
            'ssr_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-shift_schedule_request-ssr_uss_id',
            '{{%shift_schedule_request}}',
            'ssr_uss_id',
            '{{%user_shift_schedule}}',
            'uss_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request-ssr_sst_id',
            '{{%shift_schedule_request}}',
            'ssr_sst_id',
            '{{%shift_schedule_type}}',
            'sst_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request-ssr_created_user_id',
            '{{%shift_schedule_request}}',
            'ssr_created_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request-ssr_updated_user_id',
            '{{%shift_schedule_request}}',
            'ssr_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-shift_schedule_request-ssr_status_id', '{{%shift_schedule_request}}', 'ssr_status_id');
        $this->createIndex('IND-shift_schedule_request-ssr_created_user_id', '{{%shift_schedule_request}}', 'ssr_created_user_id');

        (new RbacMigrationService())->up($this->routes, $this->roles);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_request}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shift_schedule_request}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);  //  abac

        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_request}}');
    }
}
