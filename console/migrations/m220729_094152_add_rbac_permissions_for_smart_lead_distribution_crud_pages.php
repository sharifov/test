<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220729_094152_add_rbac_permissions_for_smart_lead_distribution_crud_pages
 */
class m220729_094152_add_rbac_permissions_for_smart_lead_distribution_crud_pages extends Migration
{
    private array $routes = [
        '/smart-lead-distribution/lead-rating-parameter-crud/index',
        '/smart-lead-distribution/lead-rating-parameter-crud/view',
        '/smart-lead-distribution/lead-rating-parameter-crud/create',
        '/smart-lead-distribution/lead-rating-parameter-crud/update',
        '/smart-lead-distribution/lead-rating-parameter-crud/delete',
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
}
