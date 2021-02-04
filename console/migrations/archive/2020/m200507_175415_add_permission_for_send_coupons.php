<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200507_175415_add_permission_for_send_coupons
 */
class m200507_175415_add_permission_for_send_coupons extends Migration
{
    public $route = [
        '/coupon/send',
        '/coupon/preview',
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
