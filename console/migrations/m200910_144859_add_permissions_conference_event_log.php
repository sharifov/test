<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200910_144859_add_permissions_conference_event_log
 */
class m200910_144859_add_permissions_conference_event_log extends Migration
{
    public $route = [
        '/conference-event-log/index',
        '/conference-event-log/view',
        '/conference-event-log/create',
        '/conference-event-log/update',
        '/conference-event-log/delete',
    ];

    public $roles = [
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
