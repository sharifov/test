<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200803_071136_create_tbl_user_monitor
 */
class m200803_071136_create_tbl_user_monitor extends Migration
{
    public $route = [
        '/user-monitor/*',
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

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_monitor}}', [
            'um_id' => $this->primaryKey(),
            'um_user_id' => $this->integer()->notNull(),
            'um_type_id' => $this->smallInteger()->notNull(),
            'um_start_dt' => $this->dateTime(),
            'um_end_dt' => $this->dateTime(),
            'um_period_sec' => $this->integer(),
            'um_description' => $this->string(255),
        ], $tableOptions);

        $this->addForeignKey('FK-user_monitor-um_user_id', '{{%user_monitor}}', ['um_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->createIndex('IND-user_monitor-um_user_id-um_type_id', '{{%user_monitor}}', ['um_user_id', 'um_type_id']);

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_monitor}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
