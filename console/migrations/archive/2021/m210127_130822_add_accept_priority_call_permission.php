<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210127_130822_add_accept_priority_call_permission
 */
class m210127_130822_add_accept_priority_call_permission extends Migration
{
    public $newPermission = '/call/ajax-accept-priority-call';
    public $oldPermission = '/call/ajax-accept-incoming-call';
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
