<?php

use yii\db\Migration;

/**
 * Class m201229_113930_add_permission_for_update_case_booking_id
 */
class m201229_113930_add_permission_for_update_case_booking_id extends Migration
{
    private $route = [
        '/cases/update-booking-id-by-sale'
    ];

    private $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN
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
