<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\globalRules\clientChat\ClientChatToInProgressOwnerRule;
use sales\rbac\rules\globalRules\clientChat\ClientChatToInProgressRule;
use yii\db\Migration;

/**
 * Class m201002_075746_add_permissions_cc_in_progress
 */
class m201002_075746_add_permissions_cc_in_progress extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private string $toInProgressPermissionName = 'global/client-chat/to-in_progress';
    private string $toInProgressOwnerPermissionName = 'global/client-chat/to-in_progress-owner';
    private string $toInProgressAccessPermissionName = 'client-chat/in_progress/access';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
