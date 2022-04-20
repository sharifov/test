<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220208_074711_add_rbac_permission_for_lead_rating_crud
 */

class m220208_074711_add_rbac_permission_for_lead_rating_crud extends Migration
{
    private array $routes = [
        'crud' => [
            '/lead-user-rating-crud/*',
        ],
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
