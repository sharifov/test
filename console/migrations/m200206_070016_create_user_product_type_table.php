<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\base\NotSupportedException;
use yii\db\Migration;
use yii\rbac\ManagerInterface;

/**
 * Class m200206_070016_create_user_product_type_table
 */
class m200206_070016_create_user_product_type_table extends Migration
{
     public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $permissions = [
        'user-product-type/list',
        'user-product-type/create',
        'user-product-type/update',
        'user-product-type/delete',
        'product/manage/all',
    ];

    public $tableName = 'user_product_type';
    public $table = '{{%user_product_type}}';
    public $tableOptions;

    /** @var RbacMigrationService  */
    private $rbacMigrationService;

    /** @var ManagerInterface  */
    private $authManager;

    public function init()
    {
        parent::init();
        $this->rbacMigrationService = new RbacMigrationService();
        $this->authManager = $this->rbacMigrationService->getAuth();
    }

    /**
     * @throws NotSupportedException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function safeUp(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->table,	[
            'upt_user_id' => $this->integer()->notNull(),
            'upt_product_type_id' => $this->integer()->notNull(),
            'upt_commission_percent' => $this->decimal(5, 2),
            'upt_product_enabled' => $this->boolean()->defaultValue(true),
            'upt_created_user_id' => $this->integer(),
            'upt_updated_user_id' => $this->integer(),
            'upt_created_dt' => $this->dateTime(),
            'upt_updated_dt' => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('PK-' . $this->tableName . '-user-product-type', $this->table, ['upt_user_id', 'upt_product_type_id']);

        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_user_id', $this->table, ['upt_user_id'],
            '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_product_type_id', $this->table, ['upt_product_type_id'],
            '{{%product_type}}', ['pt_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_created_user_id', $this->table, ['upt_created_user_id'],
            '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_updated_user_id', $this->table, ['upt_updated_user_id'],
            '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);

        $this->rbacMigrationService->up($this->permissions, $this->roles);
    }

    /**
     * @throws NotSupportedException
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_product_type_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_created_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_updated_user_id', $this->table);

        $this->dropTable($this->table);

        Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);

        $this->rbacMigrationService->down($this->permissions, $this->roles);
    }
}
