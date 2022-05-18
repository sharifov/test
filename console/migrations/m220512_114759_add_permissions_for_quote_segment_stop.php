<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220512_114759_add_permissions_for_quote_segment_stop
 */
class m220512_114759_add_permissions_for_quote_segment_stop extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/quote-segment-stop-crud/index',
        '/quote-segment-stop-crud/view',
        '/quote-segment-stop-crud/create',
        '/quote-segment-stop-crud/update',
        '/quote-segment-stop-crud/delete',
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
