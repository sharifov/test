<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200430_124416_add_permission_sms_list_ajax
 */
class m200430_124416_add_permission_sms_list_ajax extends Migration
{
    public $route = ['/sms/list-ajax', '/sms/send'];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
