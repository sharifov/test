<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210824_053717_correction_routes_flight_request
 */
class m210824_053717_correction_routes_flight_request extends Migration
{
    private $oldRoutes = [
        '/flight-request-crud/index',
        '/flight-request-crud/create',
        '/flight-request-crud/view',
        '/flight-request-crud/update',
        '/flight-request-crud/delete',
    ];

    private $newRoutes = [
        '/flight/flight-request-crud/index',
        '/flight/flight-request-crud/create',
        '/flight/flight-request-crud/view',
        '/flight/flight-request-crud/update',
        '/flight/flight-request-crud/delete',

        '/flight/flight-request-log-crud/index',
        '/flight/flight-request-log-crud/create',
        '/flight/flight-request-log-crud/view',
        '/flight/flight-request-log-crud/update',
        '/flight/flight-request-log-crud/delete',
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
        (new RbacMigrationService())->down($this->oldRoutes, $this->roles);
        (new RbacMigrationService())->up($this->newRoutes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->newRoutes, $this->roles);
        (new RbacMigrationService())->up($this->oldRoutes, $this->roles);
        Yii::$app->cache->flush();
    }
}
