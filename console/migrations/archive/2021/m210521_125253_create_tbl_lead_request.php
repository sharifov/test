<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210521_125253_create_tbl_lead_request
 */
class m210521_125253_create_tbl_lead_request extends Migration
{
    private $routes = [
        '/lead-request-crud/view',
        '/lead-request-crud/index',
        '/lead-request-crud/create',
        '/lead-request-crud/updated',
        '/lead-request-crud/delete',
        '/lead-data-crud/view',
        '/lead-data-crud/index',
        '/lead-data-crud/create',
        '/lead-data-crud/updated',
        '/lead-data-crud/delete',
        '/app-project-key-crud/view',
        '/app-project-key-crud/index',
        '/app-project-key-crud/create',
        '/app-project-key-crud/updated',
        '/app-project-key-crud/delete',
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

        $this->createTable('{{%lead_request}}', [
            'lr_id' => $this->primaryKey(),
            'lr_type' => $this->string(50)->notNull(),
            'lr_job_id' => $this->integer(),
            'lr_json_data' => $this->json(),
            'lr_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('{{%lead_data}}', [
            'ld_id' => $this->primaryKey(),
            'ld_lead_id' => $this->integer(),
            'ld_field_key' => $this->string(50)->notNull(),
            'ld_field_value' => $this->string(500),
            'ld_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-lead_data-lead_id-field_key', '{{%lead_data}}', ['ld_lead_id', 'ld_field_key'], true);
        $this->createIndex('IND-lead_data-field_key-field_value', '{{%lead_data}}', ['ld_field_key', 'ld_field_value']);
        $this->createIndex('IND-lead_data-lead_id', '{{%lead_data}}', ['ld_lead_id']);
        $this->addForeignKey(
            'FK-lead_data-lead',
            '{{%lead_data}}',
            ['ld_lead_id'],
            '{{%leads}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%app_project_key}}', [
           'apk_id' => $this->primaryKey(),
           'apk_key' => $this->string(50)->unique(),
           'apk_project_id' => $this->integer()->notNull(),
           'apk_project_source_id' => $this->integer()->notNull(),
           'apk_created_dt' => $this->dateTime(),
           'apk_updated_dt' => $this->dateTime(),
           'apk_created_user_id' => $this->integer(),
           'apk_updated_user_id' => $this->integer()
        ], $tableOptions);
        $this->addForeignKey(
            'FK-app_project_key-project_id',
            '{{%app_project_key}}',
            'apk_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-app_project_key-project_source_id',
            '{{%app_project_key}}',
            ['apk_project_source_id'],
            '{{%sources}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-app_project_key-created_user_id',
            '{{%app_project_key}}',
            ['apk_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-app_project_key-updated_user_id',
            '{{%app_project_key}}',
            ['apk_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_data-lead', 'lead_data');
        $this->dropForeignKey('FK-app_project_key-project_id', 'app_project_key');
        $this->dropForeignKey('FK-app_project_key-project_source_id', 'app_project_key');
        $this->dropForeignKey('FK-app_project_key-created_user_id', 'app_project_key');
        $this->dropForeignKey('FK-app_project_key-updated_user_id', 'app_project_key');

        $this->dropTable('app_project_key');
        $this->dropTable('lead_data');
        $this->dropTable('lead_request');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
