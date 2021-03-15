<?php

namespace modules\flight\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210312_140212_add_permission_for_flight_quote_option_crud_pages
 */
class m210312_140212_add_permission_for_flight_quote_option_crud_pages extends Migration
{
    private array $routes = [
        '/flight/flight-quote-option/index',
        '/flight/flight-quote-option/create',
        '/flight/flight-quote-option/read',
        '/flight/flight-quote-option/update',
        '/flight/flight-quote-option/delete',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_140212_add_permission_for_flight_quote_option_crud_pages cannot be reverted.\n";

        return false;
    }
    */
}
