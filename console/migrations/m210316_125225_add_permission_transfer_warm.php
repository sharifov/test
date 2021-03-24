<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210316_125225_add_permission_transfer_warm
 */
class m210316_125225_add_permission_transfer_warm extends Migration
{
    public $newPermission = '/call/ajax-accept-warm-transfer-call';
    public $oldPermission = '/call/ajax-accept-incoming-call';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission, $this->oldPermission);

        $auth = Yii::$app->authManager;

        $transferToUserPermission = $auth->createPermission('PhoneWidget_TransferToUser');
        $transferToUserPermission->description = 'Transfer to user';
        $auth->add($transferToUserPermission);

        $warmTransferToUserPermission = $auth->createPermission('PhoneWidget_WarmTransferToUser');
        $warmTransferToUserPermission->description = 'Warm transfer to user';
        $auth->add($warmTransferToUserPermission);

        $transferToDepartmentPermission = $auth->createPermission('PhoneWidget_TransferToDepartment');
        $transferToDepartmentPermission->description = 'Transfer to department';
        $auth->add($transferToDepartmentPermission);

        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission('PhoneWidget_TransferToUser', '/phone/ajax-call-transfer');
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission('PhoneWidget_WarmTransferToUser', '/phone/ajax-call-transfer');
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission('PhoneWidget_TransferToDepartment', '/phone/ajax-call-transfer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission, $this->oldPermission);

        $permissions = ['PhoneWidget_TransferToUser', 'PhoneWidget_WarmTransferToUser', 'PhoneWidget_TransferToDepartment'];

        $auth = Yii::$app->authManager;

        foreach ($permissions as $permission) {
            if ($p = $auth->getPermission($permission)) {
                $auth->remove($p);
            }
        }
    }
}
