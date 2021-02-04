<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200722_120949_add_permission_case_create_by_chat
 */
class m200722_120949_add_permission_case_create_by_chat extends Migration
{
    public $newPermission = '/cases/create-by-chat';
    public $oldPermission = '/cases/create';
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
