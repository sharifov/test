<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211209_141021_add_reconnect_permission
 */
class m211209_141021_add_reconnect_permission extends Migration
{
    public $newPermission = '/call/reconnect';
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
