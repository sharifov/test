<?php

namespace modules\rentCar\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210305_072013_add_column_rcq_booking_json
 */
class m210305_072013_add_column_rcq_booking_json extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/rent-car/rent-car-quote/book',
        '/rent-car/rent-car-quote/contract-request',
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rent_car_quote}}', 'rcq_booking_json', $this->json());
        $this->addColumn('{{%rent_car_quote}}', 'rcq_booking_id', $this->integer());

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_booking_json');
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_booking_id');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
