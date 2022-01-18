<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210205_123538_add_permissions_get_phone_list_id
 */
class m210205_123538_add_permissions_get_phone_list_id extends Migration
{
    public $newPermission = '/phone/ajax-get-phone-list-id';
    public $oldPermission = '/phone/ajax-hangup';
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
