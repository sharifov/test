<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200622_145006_add_permissions_case_category_report
 */
class m200622_145006_add_permissions_case_category_report extends Migration
{
    public $route = ['/case-category/report'];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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
