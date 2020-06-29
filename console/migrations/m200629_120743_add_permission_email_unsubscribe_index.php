<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200629_120743_add_permission_email_unsubscribe_index
 */
class m200629_120743_add_permission_email_unsubscribe_index extends Migration
{
    public $route = [
        '/email-unsubscribe/index',
        '/client-project/unsubscribe-client-ajax'
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
