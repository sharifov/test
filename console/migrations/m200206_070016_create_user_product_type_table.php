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
    public $routes = [
        '/user-product-type/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $mainPermission = 'userProductTypeList';

    public $subPermissions = [
        'lead_view_userProductTypeAdd',
        'lead_view_userProductTypeUpdate',
        'lead_view_userProductTypeDelete',
    ];

    public $profileUserProductTypeList = 'site_profile_userProductTypeList';

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
     * @return bool|void
     * @throws \yii\base\Exception
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        // DB migrate
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

        // RBAC migrate
        $permissionProductTypeList = $this->rbacMigrationService->getOrCreatePermission($this->mainPermission);

        foreach ($this->subPermissions as $permission) {
            $this->authManager->addChild(
                $permissionProductTypeList,
                $this->rbacMigrationService->getOrCreatePermission($permission)
            );
        }
        foreach ($this->roles as $role) {
            if ($admin = $this->authManager->getRole($role)) {
                $this->authManager->addChild($admin, $permissionProductTypeList);
            }
        }

        if ($agentRole = $this->authManager->getRole(Employee::ROLE_AGENT)) {
            $this->authManager->addChild(
                $agentRole,
                $this->rbacMigrationService->getOrCreatePermission($this->profileUserProductTypeList)
            );
        }

        $this->rbacMigrationService->up($this->routes, $this->roles);

        $this->resetCache();
    }

    /**
     * @return bool|void
     * @throws NotSupportedException
     */
    public function safeDown(): void
    {
        // DB migrate
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_product_type_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_created_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_updated_user_id', $this->table);

        $this->dropTable($this->table);

        // RBAC migrate
        foreach ($this->subPermissions as $name) {
            if ($permission = $this->authManager->getPermission($name)) {
                $this->authManager->remove($permission);
            }
        }
        if ($mainPermission = $this->authManager->getPermission($this->mainPermission)) {
            $this->authManager->remove($mainPermission);
        }
        if ($profilePermission = $this->authManager->getPermission($this->profileUserProductTypeList)) {
            $this->authManager->remove($profilePermission);
        }

        $this->rbacMigrationService->down($this->routes, $this->roles);

        $this->resetCache();
    }

    /**
     * @throws NotSupportedException
     */
    private function resetCache(): void
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);
    }
}
