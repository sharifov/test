<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200524_192236_add_permission_complete_conference_call
 */
class m200524_192236_add_permission_complete_conference_call extends Migration
{
    public $newPermission = '/phone/ajax-conference-complete';
    public $oldPermission = '/phone/ajax-call-transfer';
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
