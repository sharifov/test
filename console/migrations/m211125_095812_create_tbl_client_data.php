<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211125_095812_create_tbl_client_data
 */
class m211125_095812_create_tbl_client_data extends Migration
{
    private $routes = [
        '/client-data-key-crud/view',
        '/client-data-key-crud/index',
        '/client-data-key-crud/create',
        '/client-data-key-crud/update',
        '/client-data-key-crud/delete',
        '/client-data-crud/view',
        '/client-data-crud/index',
        '/client-data-crud/create',
        '/client-data-crud/update',
        '/client-data-crud/delete',
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

        $this->createTable('{{%client_data_key}}', [
            'cdk_id' => $this->primaryKey(),
            'cdk_key' => $this->string(50)->notNull()->unique(),
            'cdk_name' => $this->string(50)->notNull(),
            'cdk_description' => $this->string(500),
            'cdk_enable' => $this->boolean()->defaultValue(true),
            'cdk_is_system' => $this->boolean()->defaultValue(false),
            'cdk_created_dt' => $this->dateTime(),
            'cdk_updated_dt' => $this->dateTime(),
            'cdk_created_user_id' => $this->integer(),
            'cdk_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('IND-client_data_key-cdk_enable', '{{%client_data_key}}', ['cdk_enable']);

        $this->addForeignKey(
            'FK-client_data_key-created_user_id',
            '{{%client_data_key}}',
            ['cdk_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-client_data_key-updated_user_id',
            '{{%client_data_key}}',
            ['cdk_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%client_data}}', [
            'cd_id' => $this->primaryKey(),
            'cd_client_id' => $this->integer(),
            'cd_key_id' => $this->integer(),
            'cd_field_value' => $this->string(500),
            'cd_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-client_data-client_id-field_id', '{{%client_data}}', ['cd_client_id', 'cd_key_id'], true);
        $this->addForeignKey(
            'FK-client_data-key',
            '{{%client_data}}',
            ['cd_key_id'],
            '{{%client_data_key}}',
            'cdk_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-client_data-client',
            '{{%client_data}}',
            ['cd_client_id'],
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_data_key-created_user_id', '{{%client_data_key}}');
        $this->dropForeignKey('FK-client_data_key-updated_user_id', '{{%client_data_key}}');
        $this->dropForeignKey('FK-client_data-key', '{{%client_data}}');
        $this->dropForeignKey('FK-client_data-client', '{{%client_data}}');

        $this->dropTable('{{%client_data}}');
        $this->dropTable('{{%client_data_key}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
