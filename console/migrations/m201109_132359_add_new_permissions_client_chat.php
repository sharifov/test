<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201109_132359_add_new_permissions_client_chat
 */
class m201109_132359_add_new_permissions_client_chat extends Migration
{
    private $permissions = [
        'client-chat/accept-transfer',
        'client-chat/skip-transfer',
        'client-chat/accept-pending',
        'client-chat/skip-pending',
    ];

    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $acceptTransferPermission = $auth->createPermission('client-chat/accept-transfer');
        $acceptTransferPermission->description = 'Client chat accept transfer request';
        $auth->add($acceptTransferPermission);

        $skipTransferPermission = $auth->createPermission('client-chat/skip-transfer');
        $skipTransferPermission->description = 'Client chat skip transfer request';
        $auth->add($skipTransferPermission);

        $acceptPendingPermission = $auth->createPermission('client-chat/accept-pending');
        $acceptPendingPermission->description = 'Client chat accept pending request';
        $auth->add($acceptPendingPermission);

        $skipPendingPermission = $auth->createPermission('client-chat/skip-pending');
        $skipPendingPermission->description = 'Client chat skip pending request';
        $auth->add($skipPendingPermission);

        (new RbacMigrationService())->up($this->permissions, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->permissions, $this->roles);

        $auth = Yii::$app->authManager;

        foreach ($this->permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

    }

}
