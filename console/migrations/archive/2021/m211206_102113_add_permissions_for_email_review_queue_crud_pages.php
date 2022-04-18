<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211206_102113_add_permissions_for_email_review_queue_crud_pages
 */
class m211206_102113_add_permissions_for_email_review_queue_crud_pages extends Migration
{
    private $routes = [
        '/email-review-queue-crud/view',
        '/email-review-queue-crud/index',
        '/email-review-queue-crud/create',
        '/email-review-queue-crud/update',
        '/email-review-queue-crud/delete',
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
