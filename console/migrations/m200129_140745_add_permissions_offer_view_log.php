<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200129_140745_add_permissions_offer_view_log
 */
class m200129_140745_add_permissions_offer_view_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/offer/offer-view-log-crud/index',
        '/offer/offer-view-log-crud/view',
        '/offer/offer-view-log-crud/create',
        '/offer/offer-view-log-crud/update',
        '/offer/offer-view-log-crud/delete',

        '/offer/offer-view-log/show',
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
