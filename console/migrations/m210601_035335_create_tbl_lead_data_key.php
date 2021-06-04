<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210601_035335_create_tbl_lead_data_key
 */
class m210601_035335_create_tbl_lead_data_key extends Migration
{
    private $routes = [
        '/lead-data-key-crud/view',
        '/lead-data-key-crud/index',
        '/lead-data-key-crud/create',
        '/lead-data-key-crud/update',
        '/lead-data-key-crud/delete',
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

        $this->createTable('{{%lead_data_key}}', [
            'ldk_id' => $this->primaryKey(),
            'ldk_key' => $this->string(50)->notNull()->unique(),
            'ldk_name' => $this->string(50)->notNull(),
            'ldk_enable' => $this->boolean()->defaultValue(true),
            'ldk_created_dt' => $this->dateTime(),
            'ldk_updated_dt' => $this->dateTime(),
            'ldk_created_user_id' => $this->integer(),
            'ldk_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('IND-lead_data_key-ldk_enable', '{{%lead_data_key}}', ['ldk_enable']);

        $this->addForeignKey(
            'FK-lead_data_key-created_user_id',
            '{{%lead_data_key}}',
            ['ldk_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-lead_data_key-updated_user_id',
            '{{%lead_data_key}}',
            ['ldk_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->insert(
            '{{%lead_data_key}}',
            [
                'ldk_key' => 'kayakclickid',
                'ldk_name' => 'KayakClickId',
                'ldk_enable' => true,
                'ldk_created_dt' => date('Y-m-d H:i:s'),
                'ldk_updated_dt' => date('Y-m-d H:i:s'),
                'ldk_created_user_id' => null,
                'ldk_updated_user_id' => null,
            ]
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_data_key-created_user_id', '{{%lead_data_key}}');
        $this->dropForeignKey('FK-lead_data_key-updated_user_id', '{{%lead_data_key}}');

        $this->dropTable('{{%lead_data_key}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
