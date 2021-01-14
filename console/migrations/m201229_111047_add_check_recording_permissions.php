<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201229_111047_add_check_recording_permissions
 */
class m201229_111047_add_check_recording_permissions extends Migration
{
    public $newPermission = '/phone/ajax-check-recording';
    public $oldPermission = '/phone/check-black-phone';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->addNewPermissionToRolesWhoCanOldPermission($this->newPermission, $this->oldPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->removePermissionFromRolesWhoCanOtherPermission($this->newPermission, $this->oldPermission);
    }
}
