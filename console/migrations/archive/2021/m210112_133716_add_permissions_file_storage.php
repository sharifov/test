<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210112_133716_add_permissions_file_storage
 */
class m210112_133716_add_permissions_file_storage extends Migration
{
    private $routes = [
        '/file-storage/file-storage/*',
        '/file-storage/file-share/*',
        '/file-storage/file-log/*',
        '/file-storage/file-lead/*',
        '/file-storage/file-case/*',
        '/file-storage/file-client/*',
        '/file-storage/file-user/*',
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
