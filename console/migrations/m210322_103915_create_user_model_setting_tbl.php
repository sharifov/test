<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;
use yii\rbac\ManagerInterface;

/**
 * Class m210322_103915_create_user_search_setting_tbl
 */
class m210322_103915_create_user_model_setting_tbl extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    public $routes = [
        '/user-model-setting-crud/index',
        '/user-model-setting-crud/create',
        '/user-model-setting-crud/update',
        '/user-model-setting-crud/delete',
        '/user-model-setting-crud/view',
    ];

    public $tableName = 'user_model_setting';
    public $table = '{{%user_model_setting}}';
    public $tableOptions;

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->table, [
            'ums_id' => $this->primaryKey(),
            'ums_user_id' => $this->integer()->notNull(),
            'ums_name' => $this->string(50),
            'ums_key' => $this->string(50),
            'ums_type' => $this->integer(),
            'ums_class' => $this->string(255),
            'ums_settings_json' => $this->json(),
            'ums_sort_order_json' => $this->json(),
            'ums_per_page' => $this->integer()->defaultValue(30),
            'ums_enabled' => $this->boolean()->defaultValue(true),
            'ums_created_dt' => $this->dateTime(),
            'ums_updated_dt' => $this->dateTime(),
        ], $this->tableOptions);

        $this->addForeignKey(
            'FK-' . $this->tableName . '-ums_user_id',
            $this->table,
            ['ums_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable($this->table);

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
