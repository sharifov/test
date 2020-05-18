<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200129_101140_add_permissions_offer_send_log
 */
class m200129_101140_add_permissions_offer_send_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/offer/offer-send-log-crud/index',
        '/offer/offer-send-log-crud/view',
        '/offer/offer-send-log-crud/create',
        '/offer/offer-send-log-crud/update',
        '/offer/offer-send-log-crud/delete',

        '/offer/offer-send-log/show',
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
