<?php

use yii\db\Migration;

/**
 * Class m210427_124126_add_permission_transaction_list
 */
class m210427_124126_add_permission_transaction_list extends Migration
{
    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/order/transaction-actions/update',
        '/order/transaction-actions/delete',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $viewTransaction = $auth->createPermission('global/transaction/list/view');
        $viewTransaction->description = 'View transaction list';
        $auth->add($viewTransaction);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $viewTransaction);
            }
        }

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $permissions = [
            'global/transaction/list/view',
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
