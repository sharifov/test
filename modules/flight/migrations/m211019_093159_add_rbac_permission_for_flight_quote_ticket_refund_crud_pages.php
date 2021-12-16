<?php

namespace modules\flight\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211019_093159_add_rbac_permission_for_flight_quote_ticket_refund_crud_pages
 */
class m211019_093159_add_rbac_permission_for_flight_quote_ticket_refund_crud_pages extends Migration
{
    private array $routes = [
        '/flight/flight-quote-ticket-refund-crud/index',
        '/flight/flight-quote-ticket-refund-crud/view',
        '/flight/flight-quote-ticket-refund-crud/create',
        '/flight/flight-quote-ticket-refund-crud/update',
        '/flight/flight-quote-ticket-refund-crud/delete',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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
