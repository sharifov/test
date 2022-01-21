<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210119_124228_create_tbl_conference_recording_log
 */
class m210119_124228_create_tbl_conference_recording_log extends Migration
{
    private array $routes = [
        '/conference-recording-log-crud/index',
        '/conference-recording-log-crud/view',
        '/conference-recording-log-crud/create',
        '/conference-recording-log-crud/update',
        '/conference-recording-log-crud/delete',
    ];

    private array $roles = [
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

        $this->createTable('{{%conference_recording_log}}', [
            'cfrl_id' => $this->integer()->notNull(),
            'cfrl_conference_sid' => $this->string(34),
            'cfrl_user_id' => $this->integer()->notNull(),
            'cfrl_created_dt' => $this->dateTime(),
            'cfrl_year' => $this->smallInteger()->notNull(),
            'cfrl_month' => $this->tinyInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-conference_recording_log', '{{%conference_recording_log}}', ['cfrl_id', 'cfrl_year', 'cfrl_month']);
        $this->alterColumn('{{%conference_recording_log}}', 'cfrl_id', $this->integer()->notNull() . ' AUTO_INCREMENT');
        $this->createIndex('IND-conference_recording_log-cfrl_conference_sid', '{{%conference_recording_log}}', ['cfrl_conference_sid']);
        $this->createIndex('IND-conference_recording_log-cfrl_year', '{{%conference_recording_log}}', ['cfrl_year']);
        $this->createIndex('IND-conference_recording_log-cfrl_month', '{{%conference_recording_log}}', ['cfrl_month']);

        Yii::$app->db->createCommand('ALTER TABLE `conference_recording_log` PARTITION BY RANGE (`cfrl_year`)
SUBPARTITION BY LINEAR HASH (`cfrl_month`)
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
        $this->dropTable('{{%conference_recording_log}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
