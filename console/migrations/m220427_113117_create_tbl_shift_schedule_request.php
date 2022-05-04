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
            'srh_id' => $this->primaryKey(),
            'srh_uss_id' => $this->integer(),
            'srh_sst_id' => $this->integer(),
            'srh_status_id' => $this->integer()->notNull(),
            'srh_description' => $this->string(1000),
            'srh_start_utc_dt' => $this->dateTime(),
            'srh_end_utc_dt' => $this->dateTime(),
            'srh_created_dt' => $this->dateTime(),
            'srh_update_dt' => $this->dateTime(),
            'srh_created_user_id' => $this->integer(),
            'srh_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-shift_schedule_request-srh_uss_id',
            '{{%shift_schedule_request}}',
            'srh_uss_id',
            '{{%user_shift_schedule}}',
            'uss_id',
            'SET NULL',
            'SET NULL'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request-srh_sst_id',
            '{{%shift_schedule_request}}',
            'srh_sst_id',
            '{{%shift_schedule_type}}',
            'sst_id',
            'SET NULL',
            'SET NULL'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request-srh_created_user_id',
            '{{%shift_schedule_request}}',
            'srh_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'SET NULL'
        );

        $this->createIndex('IND-shift_schedule_request-srh_status_id', '{{%shift_schedule_request}}', 'srh_status_id');
        $this->createIndex('IND-shift_schedule_request-srh_created_user_id', '{{%shift_schedule_request}}', 'srh_created_user_id');

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
