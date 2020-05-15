<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200507_152755_add_permissions_coupons_request
 */
class m200507_152755_add_permissions_coupons_request extends Migration
{
    public $route = [
        '/coupon/request',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
