<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200903_063328_create_tbls_additional_call_command
 */
class m200903_063328_create_tbls_additional_call_command extends Migration
{
    public $route = [
        '/phone-line-command-crud/*',
        '/call-gather-switch-crud/*',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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

        $this->createTable('{{%phone_line_command}}', [
            'plc_id' => $this->primaryKey(),
            'plc_line_id' => $this->integer(),
            'plc_ccom_id' => $this->integer(),
            'plc_sort_order' => $this->integer()->defaultValue(5),
            'plc_created_user_id' => $this->integer(),
            'plc_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-phone_line_command-plc_line_id',
            '{{%phone_line_command}}',
            ['plc_line_id'],
            '{{%phone_line}}',
            ['line_id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-phone_line_command-plc_ccom_id',
            '{{%phone_line_command}}',
            ['plc_ccom_id'],
            '{{%call_command}}',
            ['ccom_id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-phone_line_command-plc_created_user_id',
            '{{%phone_line_command}}',
            ['plc_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%call_gather_switch}}', [
            'cgs_ccom_id' => $this->integer()->notNull(),
            'cgs_step' => $this->integer()->notNull(),
            'cgs_case' => $this->integer()->notNull(),
            'cgs_exec_ccom_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-call_gather_switch',
            '{{%call_gather_switch}}',
            ['cgs_ccom_id', 'cgs_step', 'cgs_case']
        );
        $this->addForeignKey(
            'FK-call_gather_switch-cgs_ccom_id',
            '{{%call_gather_switch}}',
            ['cgs_ccom_id'],
            '{{%call_command}}',
            ['ccom_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_gather_switch-cgs_exec_ccom_id',
            '{{%call_gather_switch}}',
            ['cgs_exec_ccom_id'],
            '{{%call_command}}',
            ['ccom_id'],
            'CASCADE',
            'CASCADE'
        );

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_gather_switch}}');
        $this->dropTable('{{%phone_line_command}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
