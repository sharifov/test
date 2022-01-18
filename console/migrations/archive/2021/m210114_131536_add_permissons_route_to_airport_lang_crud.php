<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210114_131536_add_permissons_route_to_airport_lang_crud
 */
class m210114_131536_add_permissons_route_to_airport_lang_crud extends Migration
{
    private $badRoutes = [
        '/airport-lang/*',
    ];

    private $routes = [
        '/airport-lang-crud/*',
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
        (new RbacMigrationService())->down($this->badRoutes, $this->roles);
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
