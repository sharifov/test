<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201118_114548_create_tbl_client_account
 */
class m201118_114548_create_tbl_client_account extends Migration
{
    private $routes = [
        '/client-account-crud/view',
        '/client-account-crud/index',
        '/client-account-crud/create',
        '/client-account-crud/update',
        '/client-account-crud/delete',
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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_account}}', [
            'ca_id' => $this->primaryKey(),
            'ca_project_id' => $this->integer(), /* TODO:: add req to rules */
            'ca_uuid' => $this->string(36)->notNull(),
            'ca_hid' => $this->integer()->notNull(),
            'ca_username' => $this->string(100)->notNull(),
            'ca_first_name' => $this->string(100)->notNull(),
            'ca_middle_name' => $this->string(100),
            'ca_last_name' => $this->string(100),
            'ca_nationality_country_code' => $this->string(2),
            'ca_dob' => $this->date(),
            'ca_gender' => $this->tinyInteger(),
            'ca_phone' => $this->string(100),
            'ca_subscription' => $this->boolean()->defaultValue(false),
            'ca_language_id' => $this->string(5),
            'ca_currency_code' => $this->string(3),
            'ca_timezone' => $this->string(50),
            'ca_created_ip' => $this->string(40),
            'ca_enabled' => $this->boolean()->defaultValue(true),
            'ca_origin_created_dt' => $this->dateTime(),
            'ca_origin_updated_dt' => $this->dateTime(),
            'ca_created_dt' => $this->dateTime(),
            'ca_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->createIndex('IDX-client_account-uuid', '{{%client_account}}', 'ca_uuid', true);

        $this->addForeignKey(
            'FK-client_account-ca_project_id',
            '{{%client_account}}',
            'ca_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_account-ca_language_id',
            '{{%client_account}}',
            'ca_language_id',
            '{{%language}}',
            'language_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_account-ca_currency_code',
            '{{%client_account}}',
            'ca_currency_code',
            '{{%currency}}',
            'cur_code',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%client_account_social}}', [
            'cas_ca_id' => $this->integer(),
            'cas_type_id' => $this->integer(),
            'cas_identity' => $this->string(255)->notNull(),
            'cas_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_account_social', '{{%client_account_social}}', ['cas_ca_id', 'cas_type_id']);

        $this->addForeignKey(
            'FK-client_account_social-cas_ca_id',
            '{{%client_account_social}}',
            'cas_ca_id',
            '{{%client_account}}',
            'ca_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn('{{%clients}}', 'c_ca_id', $this->integer());

        $this->addForeignKey(
            'FK-clients-c_ca_id',
            '{{%clients}}',
            'c_ca_id',
            '{{%client_account}}',
            'ca_id',
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-clients-c_ca_id', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'c_ca_id');
        $this->dropTable('{{%client_account_social}}');
        $this->dropTable('{{%client_account}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
