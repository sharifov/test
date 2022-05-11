<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211208_141500_add_new_rbca_permission_for_email_review_queue_pages
 */
class m211208_141500_add_new_rbca_permission_for_email_review_queue_pages extends Migration
{
    private $routes = [
        '/email-review-queue/pending',
        '/email-review-queue/completed',
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
