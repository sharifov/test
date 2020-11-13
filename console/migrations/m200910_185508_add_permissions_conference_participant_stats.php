<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200910_185508_add_permissions_conference_participant_stats
 */
class m200910_185508_add_permissions_conference_participant_stats extends Migration
{
    public $route = [
        '/conference-participant-stats/index',
        '/conference-participant-stats/view',
        '/conference-participant-stats/create',
        '/conference-participant-stats/update',
        '/conference-participant-stats/delete',
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
