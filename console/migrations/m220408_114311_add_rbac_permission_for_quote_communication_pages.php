<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220408_114311_add_rbac_permission_for_quote_communication_pages
 */
class m220408_114311_add_rbac_permission_for_quote_communication_pages extends Migration
{
    /**
     * @var array $sourceRoutes routes for pages, that gonna be deleted
     */
    private $sourceRoutes = [
        '/email-quote-crud/index',
        '/email-quote-crud/create',
        '/email-quote-crud/update',
        '/email-quote-crud/delete',
        '/email-quote-crud/view',
    ];

    /**
     * @var array $targetRoutes routes for pages that will be actual
     */
    private $targetRoutes = [
        '/quote-communication/*'
    ];

    /**
     * @var array $roles roles for assign to specific pages
     */
    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down($this->sourceRoutes, $this->roles);
        (new RbacMigrationService())->up($this->targetRoutes, $this->roles);
    }

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->targetRoutes, $this->roles);
        (new RbacMigrationService())->up($this->sourceRoutes, $this->roles);
    }
}
