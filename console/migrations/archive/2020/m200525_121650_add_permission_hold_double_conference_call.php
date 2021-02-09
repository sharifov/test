<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200525_121650_add_permission_hold_double_conference_call
 */
class m200525_121650_add_permission_hold_double_conference_call extends Migration
{
    public $newPermission1 = '/phone/ajax-hold-conference-double-call';
    public $newPermission2 = '/phone/ajax-unhold-conference-double-call';
    public $oldPermission = '/phone/ajax-call-transfer';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission1, $this->oldPermission);
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission2, $this->oldPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission1, $this->oldPermission);
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission2, $this->oldPermission);
    }
}
