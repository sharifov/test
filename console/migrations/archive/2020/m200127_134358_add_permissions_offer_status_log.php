<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200127_134358_add_permissions_offer_status_log
 */
class m200127_134358_add_permissions_offer_status_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/offer/offer-status-log-crud/index',
        '/offer/offer-status-log-crud/view',
        '/offer/offer-status-log-crud/create',
        '/offer/offer-status-log-crud/update',
        '/offer/offer-status-log-crud/delete',

        '/offer/offer-status-log/show',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
