<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220317_104108_add_voice_mail_record_permissions
 */
class m220317_104108_add_voice_mail_record_permissions extends Migration
{
    public $newPermission = '/voice-mail-record/record';
    public $oldPermission = '/voice-mail-record/list';
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
