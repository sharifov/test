<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201229_203936_add_permissions_recording_enable_disable
 */
class m201229_203936_add_permissions_recording_enable_disable extends Migration
{
    public $newPermissionEnable = '/phone/ajax-recording-enable';
    public $newPermissionDisable = '/phone/ajax-recording-disable';
    public $oldPermission = '/phone/ajax-mute-participant';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermissionEnable, $this->oldPermission);
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermissionDisable, $this->oldPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermissionEnable, $this->oldPermission);
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermissionDisable, $this->oldPermission);
    }
}
