<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220726_062054_add_permission_for_info_block
 */
class m220726_062054_add_permission_for_info_block extends Migration
{
    public $routes = [
        '/info-block-crud/index',
        '/info-block-crud/create',
        '/info-block-crud/view',
        '/info-block-crud/update',
        '/info-block-crud/delete',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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
