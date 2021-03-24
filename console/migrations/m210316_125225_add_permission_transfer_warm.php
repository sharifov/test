<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210316_125225_add_permission_transfer_warm
 */
class m210316_125225_add_permission_transfer_warm extends Migration
{
    public $newPermission1 = '/phone/ajax-warm-transfer-direct';
    public $oldPermission1 = '/phone/ajax-call-transfer';

    public $newPermission2 = '/call/ajax-accept-warm-transfer-call';
    public $oldPermission2 = '/call/ajax-accept-incoming-call';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission1, $this->oldPermission1);
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission2, $this->oldPermission2);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission1, $this->oldPermission1);
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission2, $this->oldPermission2);
    }
}
