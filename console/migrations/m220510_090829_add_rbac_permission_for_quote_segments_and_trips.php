<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220510_090829_add_rbac_permission_for_quote_segments_and_trips
 */
class m220510_090829_add_rbac_permission_for_quote_segments_and_trips extends Migration
{
    private array $routes = [
        '/quote-segment-crud/index',
        '/quote-segment-crud/create',
        '/quote-segment-crud/update',
        '/quote-segment-crud/delete',
        '/quote-segment-crud/view',

        '/quote-trip-crud/index',
        '/quote-trip-crud/create',
        '/quote-trip-crud/update',
        '/quote-trip-crud/delete',
        '/quote-trip-crud/view',


        '/quote-segment-baggage-crud/index',
        '/quote-segment-baggage-crud/create',
        '/quote-segment-baggage-crud/update',
        '/quote-segment-baggage-crud/delete',
        '/quote-segment-baggage-crud/view',

        '/quote-segment-baggage-charge-crud/index',
        '/quote-segment-baggage-charge-crud/create',
        '/quote-segment-baggage-charge-crud/update',
        '/quote-segment-baggage-charge-crud/delete',
        '/quote-segment-baggage-charge-crud/view',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
