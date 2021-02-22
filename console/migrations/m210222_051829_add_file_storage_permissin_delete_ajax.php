<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210222_051829_add_file_storage_permissin_delete_ajax
 */
class m210222_051829_add_file_storage_permissin_delete_ajax extends Migration
{
    private $routes = [
        '/file-storage/file-storage/delete-ajax',
        '/file-storage/file-storage/title-update',
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
