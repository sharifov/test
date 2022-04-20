<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220106_143655_alter_rbac_permission_user_auth_client
 */
class m220106_143655_alter_rbac_permission_user_auth_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down(['/auth-client/detach'], [Employee::ROLE_ADMIN, Employee::ROLE_SUPER_ADMIN]);
        (new RbacMigrationService())->up(['/user-auth-client/detach'], [Employee::ROLE_ADMIN, Employee::ROLE_SUPER_ADMIN]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
