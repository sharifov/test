<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210210_133156_add_base_permissions
 */
class m210210_133156_add_base_permissions extends Migration
{
    public $routes = [
        '/rent-car/default/*',
        '/rent-car/rent-car/*',
        '/rent-car/rent-car-crud/*',
        '/rent-car/rent-car-quote/*',
        '/rent-car/rent-car-quote-crud/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
