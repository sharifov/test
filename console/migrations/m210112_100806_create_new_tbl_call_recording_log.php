<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210112_100806_create_new_tbl_call_recording_log
 */
class m210112_100806_create_new_tbl_call_recording_log extends Migration
{
    private array $routes = [
        '/call/record',
        '/call/call-recording-log',
        '/conference/record'
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
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

        $this->createTable('{{%call_recording_log}}', [
            'crl_id' => $this->integer()->notNull(),
            'crl_call_sid' => $this->string(34),
            'crl_user_id' => $this->integer()->notNull(),
            'crl_created_dt' => $this->dateTime(),
            'crl_year' => $this->smallInteger()->notNull(),
            'crl_month' => $this->tinyInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_recording_log', '{{%call_recording_log}}', ['crl_id', 'crl_year', 'crl_month']);
        $this->alterColumn('{{%call_recording_log}}', 'crl_id', $this->integer()->notNull() . ' AUTO_INCREMENT');
        $this->createIndex('IND-call_recording_log-crl_call_sid', '{{%call_recording_log}}', ['crl_call_sid']);
        $this->createIndex('IND-call_recording_log-crl_year', '{{%call_recording_log}}', ['crl_year']);
        $this->createIndex('IND-call_recording_log-crl_month', '{{%call_recording_log}}', ['crl_month']);

        Yii::$app->db->createCommand('ALTER TABLE `call_recording_log` PARTITION BY RANGE (`crl_year`)
SUBPARTITION BY LINEAR HASH (`crl_month`)
SUBPARTITIONS 12
(
PARTITION y21 VALUES LESS THAN (2021)ENGINE = InnoDB,
PARTITION y22 VALUES LESS THAN (2022)ENGINE = InnoDB,
PARTITION y23 VALUES LESS THAN (2023)ENGINE = InnoDB,
PARTITION y24 VALUES LESS THAN (2024)ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE
 );')->execute();

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_recording_log}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
