<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201009_140934_add_permission_cc_ajax_hold_view
 */
class m201009_140934_add_permission_cc_ajax_hold_view extends Migration
{
    private $route = [
        '/client-chat/ajax-to-progress',
        '/client-chat/ajax-hold-view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
    }
}
