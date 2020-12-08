<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201116_121008_add_permission_clean_tbl
 */
class m201116_121008_add_permission_clean_tbl extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private $route = [
        '/clean/clean-table-ajax',
        '/log/clean-table',
    ];

    private $permissions = [
        'global/clean/table',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $cleanTablePermission = $auth->createPermission('global/clean/table');
        $cleanTablePermission->description = 'Access to clean table in DB';
        $auth->add($cleanTablePermission);

        (new RbacMigrationService())->up($this->permissions, $this->roles);
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
        (new RbacMigrationService())->down($this->permissions, $this->roles);

        $auth = Yii::$app->authManager;

        foreach ($this->permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }
}
